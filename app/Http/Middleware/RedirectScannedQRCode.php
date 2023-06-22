<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\QRCodeItem;

class RedirectScannedQRCode
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
        if($request->has('k')){
            $key = $request->query('k');
            $qrCodeItem = QRCodeItem::where('key', $key)->first();
            if(!$qrCodeItem){
                return redirect()->route('serial_number',['k' => $key]);
            }
            if($qrCodeItem){
                if ($qrCodeItem->url) {
                    return redirect($qrCodeItem->url);
                }else{
                    $url = url('/')."/login/?uid={$qrCodeItem->id}";
                    $path = "coupons/10-04-2023/20/{$qrCodeItem->serial_number}.png";
                    $u = $qrCodeItem->update([
                        'url' => $url,
                        'path' => $path,
                    ]);
                    return redirect($url);
                }
            }

        }
        $data = [
            'status' => 'failed',
            'message' => 'Invalid URL, Please try scanning the QR Code.'
        ];
        $request->merge($data);

        return $next($request);
    }
}
