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
Route::get('activities/activities-list', 'ActivitiesController@showActivities');    //获取活动列表
Route::get('activities/activities-reward', 'ActivitiesController@showActivitiesReward');    //获取活动奖品列表
Route::get('activities/activities-task', 'ActivitiesController@showTask');    //获取任务列表
Route::get('activities/activities-task-type', 'ActivitiesController@showTaskType');    //获取任务类型(task_type表)
Route::get('activities/activities-goods-type', 'ActivitiesController@showGoodsType');    //获取任务奖励列表