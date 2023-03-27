<?php

namespace App\Http\Controllers;

use App\Events\PaymentStatusUpdated;
use App\Models\Log;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $post           = file_get_contents('php://input');
        $secret         = env('ADAMSPAY_API_SECRET');
        $hmacExpected   = md5('adams' . $post . $secret);
        $hmacRecived    = $request->header('x-adams-notify-hash');

        // if ($hmacExpected !== $hmacRecived) return response()->json([], 401);

        $log = new Log();
        $log->data = json_encode($post);
        $log->save();

        PaymentStatusUpdated::dispatch(json_encode($post));

        return response()->json(json_encode($post));
    }
}
