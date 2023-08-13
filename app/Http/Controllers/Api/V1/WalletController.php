<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function transactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = WalletTransaction::where('user_id', $request->user()->id)
        ->when($request['type'] && $request['type']=='order', function($query){
            $query->whereIn('transaction_type', ['order_place', 'order_refund','partial_payment']);
        })
        ->when($request['type'] && $request['type']=='loyalty_point', function($query){
            $query->whereIn('transaction_type', ['loyalty_point']);
        })
        ->when($request['type'] && $request['type']=='add_fund', function($query){
            $query->whereIn('transaction_type', ['add_fund']);
        })
        ->when($request['type'] && $request['type']=='referrer', function($query){
            $query->whereIn('transaction_type', ['referrer']);
        })
        ->latest()->paginate($request->limit, ['*'], 'page', $request->offset);

        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request->limit,
            'offset' => $request->offset,
            'data' => $paginator->items()
        ];
        return response()->json($data, 200);
    }
}
