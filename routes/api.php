<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//游戏数据库接口（for web）
Route::get('records', 'RecordController@show');    //列出所有玩家的所有战绩（暂未使用）
Route::post('records', 'RecordController@search'); //查询玩家战绩
Route::post('record-info', 'RecordController@searchRecordInfo');    //根据战绩id查询单条战绩详情
Route::get('players', 'PlayerController@show');    //列出所有玩家
Route::get('players/online/amount', 'PlayerController@showOnlineAmount');   //列出实时在线玩家数量
Route::get('players/online/peak', 'PlayerController@showOnlinePeak');       //列出指定日期的当日玩家最高在线数量
Route::get('players/in-game', 'PlayerController@showInGameCount');  //实时游戏中的玩家数量
Route::get('players/in-game/peak', 'PlayerController@showInGamePeak');  //列出指定日期的当日最高在游戏中的玩家数量
Route::post('players', 'PlayerController@search'); //查询玩家
Route::post('players/find', 'PlayerController@find'); //通过uid精确查找玩家
Route::post('players/batch-find', 'PlayerController@batchFind'); //通过uids批量查找玩家
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
Route::get('activities/reward/list', 'ActivityRewardController@showActivityReward');    //获取活动奖品抽取日志(抽取数量)
Route::get('activities/reward/log', 'ActivityRewardController@getActivityRewardLog');    //获取活动奖品列表(关联奖品获取总数关系)
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
Route::get('activities/user-goods/list', 'UserGoodsController@showUserGoods');    //获取user_goods列表
Route::post('activities/user-goods/add', 'UserGoodsController@addUserGoods');
Route::post('activities/user-goods/modify', 'UserGoodsController@updateUserGoods');
Route::post('activities/user-goods/delete', 'UserGoodsController@deleteUserGoods');
Route::post('activities/user-goods/reset', 'UserGoodsController@resetUserGoods');   //重置玩家物品
Route::get('activities/tasks-player/list', 'TasksPlayerController@showTasksPlayer');    //获取tasks_player列表
Route::post('activities/tasks-player/add', 'TasksPlayerController@addTasksPlayer');
Route::post('activities/tasks-player/modify', 'TasksPlayerController@updateTasksPlayer');
Route::post('activities/tasks-player/delete', 'TasksPlayerController@deleteTasksPlayer');
Route::post('activities/tasks-player/reset', 'TasksPlayerController@resetTasksPlayer'); //重置玩家任务
Route::get('activities/log-activity-reward', 'LogActivityRewardController@show');   //查看玩家中奖记录
Route::post('wechat/official-account/unionid-openid/create', 'WechatUnionidOpenidController@create');   //创建unionid和公众号openid记录
Route::post('wechat/official-account/unionid-openid/delete', 'WechatUnionidOpenidController@destroy');  //删除记录
Route::get('wechat/red-packet/send-list', 'WechatRedPacketController@getSendList'); //获取待发送红包列表
Route::post('wechat/red-packet/update', 'WechatRedPacketController@updateSendStatus');  //更新发送红包状态
Route::post('community/record/search', 'RoomController@searchCommunityRoomRecord'); //查询社区玩家战绩
Route::post('community/record/mark', 'RoomController@markRecord');  //标记战绩为已读/未读
Route::get('community/room/open', 'CommunityRoomController@getCommunityOpenRoom'); //获取社团开房信息(正在玩的房间)

//给游戏后端调用的接口
Route::group([
    'prefix' => 'game',     //给游戏后端调用的接口
    'namespace' => 'Web',   //操作web数据库
], function () {
    Route::post('community/member/application', 'CommunityMemberController@apply2JoinCommunity'); //申请加入群
    Route::get('community/member/invitation/{player}', 'CommunityMemberController@getInvitationApplicationList')->where('player', '[0-9]+'); //获取入群邀请(和申请纪录)列表
    Route::post('community/member/approval-invitation/{invitation}', 'CommunityMemberController@approveInvitation')->where('invitation', '[0-9]+'); //同意入群请求
    Route::post('community/member/decline-invitation/{invitation}', 'CommunityMemberController@declineInvitation')->where('invitation', '[0-9]+'); //拒绝入群请求
    Route::post('community/member/quit', 'CommunityMemberController@quitCommunity');
    Route::get('community/involved/{player}', 'CommunityController@getPlayerInvolvedCommunities')->where('player', '[0-9]+'); //获取此玩家有关联的社区id
    Route::post('community/card/consumption/{community}', 'CommunityCardController@consumeCard')->where('community', '[0-9]+');//社团耗卡
    Route::get('community/info/{communityId}', 'CommunityController@getCommunityInfo')->where('communityId', '[0-9]+'); //获取社团信息
    Route::put('community/info/{community}', 'CommunityController@editCommunityInfo')->where('community', '[0-9]+'); //编辑社团信息
    Route::get('community/members/info/{community}', 'CommunityMemberController@getCommunityMembersInfo')->where('community', '[0-9]+'); //获取社团成员信息
    Route::post('community/room/open/{communityId}', 'CommunityRoomController@getCommunityOpenRoom')->where('communityId', '[0-9]+'); //获取社团开房信息(正在玩的房间)
});
