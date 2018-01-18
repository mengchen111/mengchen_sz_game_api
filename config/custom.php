<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 9/23/17
 * Time: 15:47
 */

return [
    //是否开启系统日志记录功能
    'operation_log' => env('OPERATION_LOG', true),

    //是否开启邮件通知(新的库存申请，通知管理员)
    'email_notification' => env('EMAIL_NOTIFICATION', false),

    //计划任务日志
    'cron_task_log' => env('CRON_TASK_LOG', '/tmp/artisan.log'),

    'api_key' => env('API_KEY'),
    'api_secret' => env('API_SECRET'),

    //游戏端接口地址
    'game_server_api_address' => env('GAME_SERVER_API_ADDRESS'),
    'game_server_partner_id' => env('GAME_SERVER_PARTNER_ID'),

    //游戏端接口uri
    'game_server_api_topUp' => 'recharge.php',      //玩家充值
    'game_server_api_roomCreate' => 'room_create.php',   //创建游戏房间

    //游戏端接口地址（新的调用形式）
    'game_server_api_address_new' => env('GAME_SERVER_API_ADDRESS_NEW'),
    'game_server_partner_id_new' => env('GAME_SERVER_PARTNER_ID'),
];