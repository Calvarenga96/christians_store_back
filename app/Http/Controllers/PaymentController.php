<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDebtRequest;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function createDebt(CreateDebtRequest $request)
    {
        $userId = $request->userId;
        $url    = 'https://staging.adamspay.com/api/v1/debts';
        $apiKey = env('ADAMSPAY_API_KEY');
        $docId  = Str::uuid();
        $start  = Carbon::now()->toAtomString();
        $end    = Carbon::now()->addDays(7)->toAtomString();

        $data = [
            'debt' => [
                'docId'         => $docId,
                'amount'        => [
                    'currency'  => 'PYG',
                    'value'     => (string) $request->value,
                ],
                'label'         => $request->description,
                'target'        => [
                    'type'      => 'cip',
                    'number'    => (string) $request->cip,
                    'label'     => $request->name,
                ],
                'validPeriod'   => [
                    'start'     => $start,
                    'end'       => $end,
                ],
            ]
        ];

        $headers = [
            'apikey'        => $apiKey,
            'Content-Type'  => 'application/json'
        ];

        $response           = Http::withHeaders($headers)->post($url, $data);
        $responseBody       = json_decode($response->body());
        $statusCodeFromAdam = $response->status();

        $payment            = new Payment();
        $payment->doc_id    = $docId;
        $payment->user_id   = $userId;
        $payment->product   = $request->description;
        $payment->value     = $request->value;
        $payment->status    = 'pending';
        $payment->save();

        return response()->json([
            'response'  => $responseBody,
            'debt_id'   => $payment->doc_id
        ], $statusCodeFromAdam);
    }

    public function showPayments($user_id)
    {
        $payments = Payment::where('user_id', $user_id)->get();
        return response()->json($payments, 200);
    }
}
