<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Models\Players;
use App\Models\ServerRooms;
use App\Models\Web\CommunityList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web\CommunityInvitationApplication;
use Illuminate\Support\Facades\DB;
use App\Services\Web\CommunityService;
use App\Models\Web\CommunityMemberLog;

class CommunityMemberController extends Controller
{
    /**
     *
     * @SWG\Post(
     *     path="/game/community/member/application",
     *     description="申请(邀请)加入牌艺馆",
     *     operationId="community.member.application.post",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="player_id",
     *         description="玩家id",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="community_id",
     *         description="牌艺馆id",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         description="类型(0-申请，1-邀请)，默认为0",
     *         in="query",
     *         required=false,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="参数验证错误",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/ValidationError"),
     *             },
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="逻辑验证错误",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/ApiError"),
     *             },
     *         ),
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="申请(邀请)成功",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Success"),
     *             },
     *         ),
     *     ),
     * )
     */
    public function apply2JoinCommunity(Request $request)
    {
        $this->validate($request, [
            'player_id' => 'required|integer|exists:account,id',
            'community_id' => 'required|integer|exists:mysql-web.community_list,id',
            'type' => 'nullable|integer|in:0,1',
        ]);
        $playerId = $request->input('player_id');
        $communityId = $request->input('community_id');

        $this->checkIfInTheCommunity($playerId, $communityId);
        $this->checkIfDuplicatedApplication($playerId, $communityId);

        //邀请或申请类型（0-申请，1-邀请）(默认为0)
        $type = $request->has('type') ? $request->input('type') : 0;
        CommunityInvitationApplication::create([
            'player_id' => $playerId,
            'community_id' => $communityId,
            'status' => 0,
            'type' => $type,
        ]);

        return [
            'code' => -1,
            'data' => '申请(邀请)成功',
        ];
    }

    protected function checkIfDuplicatedApplication($playerId, $communityId)
    {

        $invitation = CommunityInvitationApplication::where('player_id', $playerId)
            ->where('community_id', $communityId)
            ->where('status', 0)
            ->first();

        //已经存在的邀请
        if (!empty($invitation)) {
            throw new ApiException('已经发送过申请(邀请)请求');
        }

        return true;
    }

    protected function checkIfInTheCommunity($playerId, $communityId)
    {
        $community = CommunityList::findOrFail($communityId);
        if ($community->ifHasMember($playerId)) {
            throw new ApiException('已处于此牌艺馆中，无需申请(邀请)');
        }
        if ((int)$community->owner_player_id === (int) $playerId) {
            throw new ApiException('您已经是此牌艺馆的馆主，无需申请(邀请)');
        }
        return true;
    }

    //获取入群申请邀请列表
    public function getInvitationApplicationList(Request $request, Players $player)
    {
        $data = [];
        $data['invitation'] = $this->getInvitationList($player->id);
        $data['application'] = $this->getApplicationRecord($player->id);

        return [
            'code' => -1,
            'data' => $data,
        ];
    }

    protected function getInvitationList($playerId)
    {
        return CommunityInvitationApplication::with('community.ownerPlayer')
            ->where('player_id', $playerId)
            ->where('type', 1)
            ->where('status', 0)
            ->get();
    }

    //获取玩家的申请纪录
    protected function getApplicationRecord($playerId)
    {
        $statusMap = [
            '申请中', '通过', '拒绝'
        ];
        $applications = CommunityInvitationApplication::with('community.ownerPlayer')
            ->where('player_id', $playerId)
            ->where('type', 0)
            ->get();
        foreach ($applications as $application) {
            $application->status = $statusMap[$application->status];
        }
        return $applications;
    }

    protected function checkInvitation($invitation)
    {
        if ((int)$invitation->status !== 0) {
            throw new ApiException('此条申请已被审批');
        } else {
            return true;
        }
    }

    //同意加入群
    public function approveInvitation(Request $request, CommunityInvitationApplication $invitation)
    {
        $this->checkInvitation($invitation);

        $playerId = $invitation->player_id;
        $communityId = $invitation->community_id;
        $this->checkPlayerCommunityLimit($playerId, $communityId);

        $community = CommunityList::findOrFail($communityId);

        DB::transaction(function () use ($community, $invitation) {
            $invitation->status = 1;   //更新申请状态为已通过
            $invitation->save();

            //添加成员到community_list中相应的行中
            $newMembers = [];
            array_push($newMembers, $invitation->player_id);
            $community->addMembers($newMembers);

            //记录成员变动日志
            CommunityMemberLog::create([
                'community_id' => $invitation->community_id,
                'player_id' => $invitation->player_id,
                'action' => '加入',
            ]);

        });

        return [
            'code' => -1,
            'data' => '加入牌艺馆成功',
        ];
    }

    //检查玩家的最大入群数量
    protected function checkPlayerCommunityLimit($playerId, $communityId)
    {
        $communityConf = CommunityService::getCommunityConf($communityId);
        $communityLimit = $communityConf->max_community_count;
        $playerInvolvedCommunitiesCount = CommunityService::playerInvolvedCommunitiesTotalCount($playerId);
        if ($playerInvolvedCommunitiesCount >= $communityLimit) {
            throw new ApiException('每个玩家最多只可以加入(包括拥有)' . $communityLimit . '个牌艺馆');
        }
    }

    //拒绝加入群
    public function declineInvitation(Request $request, CommunityInvitationApplication $invitation)
    {
        $this->checkInvitation($invitation);

        $invitation->status = 2;   //更新申请状态为已拒绝
        $invitation->save();

        return [
            'code' => -1,
            'data' => '操作成功',
        ];
    }

    //退出社团
    public function quitCommunity(Request $request)
    {
        $this->validate($request, [
            'player_id' => 'required|integer|exists:mysql.account,id',
            'community_id' => 'required|integer|exists:mysql-web.community_list,id',
        ]);
        $params = $request->only(['player_id', 'community_id']);

        $community = CommunityList::find($params['community_id']);
        if (! $community->ifHasMember($params['player_id'])) {
            throw new ApiException('此玩家不存在于该牌艺馆，无法退出');
        }
        $this->doQuitCommunity($community, $params['player_id']);

        return [
            'code' => -1,
            'data' => '退出成功',
        ];
    }

    protected function doQuitCommunity($community, $playerId)
    {
        DB::transaction(function () use ($community, $playerId) {
            //删除成员
            $community->deleteMembers([$playerId]);

            //记录成员变动日志
            CommunityMemberLog::create([
                'community_id' => $community->id,
                'player_id' => $playerId,
                'action' => '退出',
            ]);
        });
    }

    /**
     *
     * @SWG\Get(
     *     path="/game/community/members/info/{community}",
     *     description="获取社团玩家信息",
     *     operationId="community.members.info.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="community",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="玩家信息",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="array",
     *                 @SWG\Items(
     *                     type="object",
     *                     allOf={
     *                         @SWG\Schema(ref="#/definitions/WebCommunityMemberInfo"),
     *                     }
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function getCommunityMembersInfo(Request $request, CommunityList $community)
    {
        return $this->res($community->members_info);
    }

    /**
     *
     * @SWG\Delete(
     *     path="/game/community/member/kick-out",
     *     description="从牌艺馆中踢出玩家",
     *     operationId="community.members.kick-out",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="player_id",
     *         description="玩家id",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="community_id",
     *         description="社团id",
     *         in="query",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=422,
     *         description="参数验证错误",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/ValidationError"),
     *             },
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="逻辑验证错误",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/ApiError"),
     *             },
     *         ),
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="踢出玩家成功",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Success"),
     *             },
     *         ),
     *     ),
     * )
     */
    public function kickMemberOut(Request $request)
    {
        $this->validate($request, [
            'player_id' => 'required|integer|exists:account,id',
            'community_id' => 'required|integer|exists:mysql-web.community_list,id',
        ]);
        $playerId = $request->player_id;

        $this->checkIfPlayerInGame($playerId);

        $community = CommunityList::findOrFail($request->community_id);
        if (! $community->ifHasMember($playerId)) {
            throw new ApiException('此玩家不存在于该牌艺馆，无法踢出');
        }
        $this->doKickOutMember($community, $playerId);

        return $this->res('踢出成员成功');
    }

    protected function checkIfPlayerInGame($playerId)
    {
        //获取正在玩的房间数据
        $openRooms = ServerRooms::all()->toArray();
        $inGameUids = [];
        foreach ($openRooms as $openRoom) {
            $uids = collect($openRoom)
                ->only(['creator_uid', 'uid_1', 'uid_2', 'uid_3', 'uid_4'])
                ->flatten()
                ->toArray();
            $inGameUids = array_merge($inGameUids, $uids);
        }
        if (in_array($playerId, $inGameUids)) {
            throw new ApiException('此玩家正在游戏中，禁止踢出操作');
        } else {
            return true;
        }
    }

    protected function doKickOutMember($community, $playerId)
    {
        DB::transaction(function () use ($community, $playerId) {
            //踢出成员
            $abandonedMembers = [];
            array_push($abandonedMembers, $playerId);
            $community->deleteMembers($abandonedMembers);

            //记录成员变动日志
            CommunityMemberLog::create([
                'community_id' => $community->id,
                'player_id' => $playerId,
                'action' => '踢出',
            ]);

            //todo 社区踢出玩家需要通知游戏后端
        });
    }

    /**
     *
     * @SWG\Get(
     *     path="/game/community/member/log/{community}",
     *     description="获取牌艺馆成员动态日志",
     *     operationId="community.member.log.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="community",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="成员动态日志",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="array",
     *                 @SWG\Items(
     *                     type="object",
     *                     allOf={
     *                         @SWG\Schema(ref="#/definitions/WebCommunityMemberLog"),
     *                     },
     *                     @SWG\Property(
     *                         property="player",
     *                         type="object",
     *                         allOf={
     *                             @SWG\Schema(ref="#/definitions/WebCommunityMemberInfo"),
     *                         },
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function getMemberLog(Request $request, CommunityList $community)
    {
        return $this->res($community->member_log);
    }
}
