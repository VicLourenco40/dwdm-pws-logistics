<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\Status;
use App\Models\OrderStatus;
use App\Http\Requests\UpdateStatusRequest;

class OrderController extends Controller
{
    public function index() {
        $orders = Order::with(['recipient', 'currentStatus'])->simplePaginate(10);

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function show(Request $request) {
        $tracking_code = $request->trackingCode;

        $order = Order::where('tracking_code', $tracking_code)->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    public function store(Request $request) {
        if (!$this->checkIfWithinTimeRange()) {
            return response()->json([
                'error' => 'You can only place orders between 9h and 16h'
            ]);
        }

        DB::beginTransaction();

        try {
            $recipient = new Recipient();
            $recipient->first_name = $request->first_name;
            $recipient->last_name = $request->last_name;
            $recipient->address = $request->address;
            $recipient->postal_code = $request->postal_code;
            $recipient->save();

            $tracking_code = $this->generateTrackingNumber();

            $order = new Order();
            $order->description = $request->description;
            $order->recipient_id = $recipient->id;
            $order->tracking_code = $tracking_code;
            $order->save();

            $pendingStatus = Status::where('slug', 'pending')->first();

            $orderStatus = new OrderStatus;
            $orderStatus->order_id = $order->id;
            $orderStatus->status_id = $pendingStatus->id;
            $orderStatus->changed_by = auth()->user()->id;
            $orderStatus->save();

            DB::commit();

            return response()->json([
                'order' => $order
            ]);
        } catch (\Exception $error) {
            DB::rollback();

            return response()->json([
                'error' => $error
            ]);
        }
    }

    private function checkIfWithinTimeRange() {
        $hour = now()->hour;

        return 9 <= $hour && $hour <= 16 && now()->isWeekDay();
    }

    private function generateTrackingNumber() {
        $letters = strtoupper(Str::random(3));
        $numbers = rand(10000, 99999);
        $tracking_number = $letters . $numbers;

        return $tracking_number;
    }

    public function updateStatus(UpdateStatusRequest $request) {
        $tracking_number = $request->code;
        $new_status_id = $request->new_status_id;

        $order = Order::with('currentStatus')->where('code', $tracking_number)->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order does not exist'
            ], 404);
        }

        $new_status = Status::where('id', $new_status_id)->first();

        if (!$new_status) {
            return response()->json([
                'error' => 'Invalid status'
            ]);
        }

        $allowedTransitions = [
            'pending' => ['available-for-distribution', 'cancelled'],
            'available-for-distribution' => ['in-transit', 'cancelled'],
            'in-transit' => ['delivered', 'cancelled'],
            'delivered' => ['returned'],
            'returned' => [],
            'cancelled' => []
        ];

        $current_status = $order->currentStatus->status->slug;

        if (!in_array($new_status->slug, $allowedTransitions[$current_status])) {
            return response()->json([
                'message' => 'Can\'t update to requested status',
                'current_status' => $current_status,
                'new_status' => $new_status->slug
            ]);
        }

        $order_status = new OrderStatus();
        $order_status->order_id = $order->id;
        $order_status->status_id = $new_status_id;
        $order_status->changed_by = auth()->user()->id;
        $order_status->save();

        return response()->json([
            'new_order_status' => $order_status
        ]);
    }
}
