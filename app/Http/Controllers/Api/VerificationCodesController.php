<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;
//        dd(!app()->environment('production'));
        if(!app()->environment('production')){
            $code = '1234';
        }else{
            //        生成4位随机数
            $code = str_pad(random_int(1,9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'template' => '1',
                    'data' => [$code],
                ]);
            }catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
                $message = $exception->getException('yuntongxun')->getMessage();
                return $this->response->errorInternal($message ?: '短信发送异常');
            }
        }
        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes(10);

//        缓存验证码，10分钟过期
        Cache::put($key,['phone'=>$phone,'code'=>$code],$expiredAt);

        return $this->response->array(
            ['key'=>$key,'expired_at'=>$expiredAt->toDateTimeString()]
        )->setStatusCode(201);
    }
}
