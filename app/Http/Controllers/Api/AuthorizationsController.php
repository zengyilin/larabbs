<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;

class AuthorizationsController extends Controller
{
    public function socialStore ($type, SocialAuthorizationRequest $request)
    {
        if(!in_array($type,['weixin'])){
            $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);
//        dd($driver);

        try{
            if($code = $request->code){
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            }else {
                $token = $request->access_token;
                if($type == 'weixin'){
                    $driver->setOpenId($request->openid);
                }
            }
            $oauthUser = $driver->userFromToken($token);
//            dd($oauthUser);

        }catch (\Exception $e){
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;
                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

//                如果没有用户
                if(!$user){
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;
        }
        return $this->response->array(['token' => $user->id]);
    }
}
