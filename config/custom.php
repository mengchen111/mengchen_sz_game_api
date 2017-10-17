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
];