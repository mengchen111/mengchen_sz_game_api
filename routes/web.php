<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', 'HomeController@index');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

//公共接口
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('info', 'InfoController@info');
});

//管理员接口
Route::group([
    'middleware' => ['auth'],
    'prefix' => 'admin/api',
    'namespace' => 'Admin'
], function () {
    Route::put('password', 'AdminController@updatePass');
    Route::get('home', 'HomeController@show');
    Route::get('system/log', 'SystemController@showLog');
});

//管理员视图路由
Route::group([
    'middleware' => ['auth'],
    'prefix' => 'admin',
    'namespace' => 'Admin'
], function () {
    Route::get('home', 'ViewController@home');
    Route::get('system/log', 'ViewController@systemLog');
});*/

//游戏接口
Route::get('records', 'RecordController@show');    //列出所有玩家的所有战绩（暂未使用）
Route::post('records', 'RecordController@search'); //查询玩家战绩
Route::post('record-info', 'RecordController@searchRecordInfo');    //根据战绩id查询单条战绩详情
Route::get('players', 'PlayerController@show');    //列出所有玩家
Route::get('players/online/amount', 'PlayerController@showOnlineAmount');   //列出实时在线玩家数量
Route::get('players/online/peak', 'PlayerController@showOnlinePeak');       //列出指定日期的当日玩家最高在线数量
Route::get('players/in-game', 'PlayerController@showInGameCount');  //实时游戏中的玩家数量
Route::get('players/in-game/peak', 'PlayerController@showInGamePeak');  //列出指定日期的当日最高在游戏中的玩家数量
Route::post('players', 'PlayerController@search'); //查询玩家
Route::post('top-up', 'PlayerController@topUp'); //玩家充值
Route::post('room', 'RoomController@create');   //创建游戏房间
Route::get('room/open', 'RoomController@showOpenRoom');  //查看正在玩的房间
Route::get('room/history', 'RoomController@showRoomHistory');  //查看已经结束的房间
Route::get('card/consumed', 'CardConsumedController@getCardConsumedData');
Route::get('card/consumed/total', 'CardConsumedController@getCardConsumedSumTotal');
Route::get('currency/log', 'CurrencyConsumedController@getCurrencyLog');    //获取道具消费记录
Route::get('activities/list', 'ActivitiesController@showActivities');    //获取活动列表
Route::post('activities/add', 'ActivitiesController@addActivities');    //添加活动
Route::post('activities/modify', 'ActivitiesController@updateActivities');    //编辑活动
Route::post('activities/delete', 'ActivitiesController@deleteActivities');    //删除活动
Route::get('activities/reward/list', 'ActivityRewardController@showActivitiesReward');    //获取活动奖品列表
Route::post('activities/reward/add', 'ActivityRewardController@addActivityReward');    //添加活动奖品
Route::post('activities/reward/modify', 'ActivityRewardController@updateActivityReward');    //编辑活动奖品
Route::post('activities/reward/delete', 'ActivityRewardController@deleteActivityReward');    //删除活动奖品
Route::get('activities/goods-type/list', 'GoodsTypeController@showGoodsType');    //获取任务奖品道具列表
Route::post('activities/goods-type/add', 'GoodsTypeController@addGoodsType');    //添加任务奖品道具
Route::post('activities/goods-type/modify', 'GoodsTypeController@updateGoodsType');    //修改任务奖品道具
Route::post('activities/goods-type/delete', 'GoodsTypeController@deleteGoodsType');    //删除任务奖品道具
Route::get('activities/task/list', 'TasksController@showTask');    //获取任务列表
Route::post('activities/task/add', 'TasksController@addTask');    //添加任务
Route::post('activities/task/modify', 'TasksController@updateTask');    //编辑任务
Route::post('activities/task/delete', 'TasksController@deleteTask');    //删除任务
Route::get('activities/task-type/list', 'TaskTypeController@showTaskType');    //获取任务类型(task_type表)
Route::post('wechat/official-account/unionid-openid/create', 'WechatUnionidOpenidController@create');   //创建unionid和公众号openid记录
Route::post('wechat/official-account/unionid-openid/delete', 'WechatUnionidOpenidController@destroy');  //删除记录
Route::get('wechat/red-packet/send-list', 'WechatRedPacketController@getSendList'); //获取待发送红包列表
Route::post('wechat/red-packet/update', 'WechatRedPacketController@updateSendStatus');  //更新发送红包状态