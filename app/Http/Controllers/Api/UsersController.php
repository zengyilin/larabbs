<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UsersRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UsersController extends Controller
{
    //
    public function store(UsersRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);
//        dd($verifyData);
        if(!$verifyData){
            return $this->response->error('验证码已失效',422);
        }
        if(!hash_equals($verifyData['code'],$request->verification_code)){
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name'=>$request->name,
            'phone'=>$verifyData['phone'],
            'password'=>bcrypt($request->password)
        ]);
//        dd($user);
        Cache::forget($request->verification_key);

        return $this->response->created();
    }
}
