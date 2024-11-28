<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\Status;
use App\Models\OrderStatus;

class OrderController extends Controller
{
    public function index() {
        $orders = Order::with('recipient')->simplePaginate(10);

        return response()->json([
            'orders' => $orders
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
}
