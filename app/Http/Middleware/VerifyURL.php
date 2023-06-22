<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\QRCodeItem;

class VerifyURL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $qrcodeId = $request->get('uid');

        $getQRCodeDetail = QRCodeItem::where(['id' => $qrcodeId,'status' => 1])->first(['is_redeemed']);

        if(!$getQRCodeDetail){
            $data = [
                'status' => 'failed',
                'message' => 'Invalid URL, Please try scanning the QR Code.'
            ];
            $request->merge($data);
            return $next($request);
        }
        if($getQRCodeDetail->is_redeemed==1){
            $data = [
                'status' => 'failed',
                'message' => 'This Coupon has already been redeemed.'
            ];
            $request->merge($data);

            return $next($request);
        }
        return $next($request);
    }
}
