<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $post           = file_get_contents('php://input');
        $secret         = env('ADAMSPAY_API_SECRET');
        $hmacExpected   = md5('adams' . $post . $secret);
        $hmacRecived    = $request->header('x-adams-notify-hash');

        $log = new Log();
        $post = json_decode($post, true);
        $log->data = json_encode([$post, $hmacExpected, $hmacRecived]);
        $log->save();

        if ($hmacExpected !== $hmacRecived) return response()->json([], 401);

        if ($request->notify->type === 'debtStatus') {
            $docId              = $request->debt->docId;
            $payment            = Payment::where('doc_id', $docId)->get();
            $payment->status    = $request->debt->payStatus->status;
            $payment->save();
            $log->data = json_encode([$post, $payment]);
        } else {
            $log->data = json_encode([$post, $request->notify->type]);
        }

        $log->save();

        return response()->json([], 204);
    }
}
