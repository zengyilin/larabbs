<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/2
 * Time: 13:47
 */
return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'yuntongxun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'yuntongxun' => [
            'app_id' => env('YUNTONGXUN_APP_ID'),
            'account_sid' => env('YUNTONGXUN_ACCOUNT_SID'),
            'account_token' => env('YUNTONGXUN_ACCOUNT_TOKEN'),
            'is_sub_account' => false
        ],
    ],
];