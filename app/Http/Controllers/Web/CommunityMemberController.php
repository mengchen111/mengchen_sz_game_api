<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Models\Players;
use App\Models\Web\CommunityList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web\CommunityInvitationApplication;
use Illuminate\Support\Facades\DB;
use App\Services\Web\CommunityService;
use App\Models\Web\CommunityMemberLog;

class CommunityMemberController extends Controller
{
    public function apply2JoinCommunity(Request $request)
    {
        $this->validate($request, [
            'player_id' => 'required|integer|exists:account,id',
            'community_id' => 'required|integer|exists:mysql-web.community_list,id',
        ]);
        $playerId = $request->input('player_id');
        $communityId = $request->input('community_id');

        $this->checkIfInTheCommunity($playerId, $communityId);
        $this->checkIfDuplicatedApplication($playerId, $communityId);

        CommunityInvitationApplication::create([
            'player_id' => $playerId,
            'community_id' => $communityId,
            'status' => 0,
            'type' => 0
        ]);

        return [
            'code' => -1,
            'data' => '申请成功',
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
            throw new ApiException('已经发送过申请请求');
        }

        return true;
    }

    protected function checkIfInTheCommunity($playerId, $communityId)
    {
        $community = CommunityList::findOrFail($communityId);
        if ($community->ifHasMember($playerId)) {
            throw new ApiException('已处于此牌艺馆中，无需申请');
        }
        if ((int)$community->owner_player_id === $playerId) {
            throw new ApiException('您已经是此牌艺馆的馆主，无需申请');
        }
        return true;
    }

    //获取入群申请邀请列表
    public function getInvitationList(Request $request, Players $player)
    {
        $invitations = CommunityInvitationApplication::with(['community'])
            ->where('player_id', $player->id)
            ->where('type', 1)
            ->where('status', 0)
            ->get();

        return [
            'code' => -1,
            'data' => $invitations,
        ];
    }

    //同意加入群
    public function approveInvitation(Request $request, CommunityInvitationApplication $application)
    {
        if ((int)$application->status !== 0) {
            throw new ApiException('此条申请已被审批');
        }

        $playerId = $application->player_id;
        $communityId = $application->community_id;
        $this->checkPlayerCommunityLimit($playerId, $communityId);

        $community = CommunityList::findOrFail($communityId);

        DB::transaction(function () use ($community, $application) {
            $application->status = 1;   //更新申请状态为已通过
            $application->save();

            //添加成员到community_list中相应的行中
            $newMembers = [];
            array_push($newMembers, $application->player_id);
            $community->addMembers($newMembers);

            //记录成员变动日志
            CommunityMemberLog::create([
                'community_id' => $application->community_id,
                'player_id' => $application->player_id,
                'action' => '加入',
            ]);

            //todo 社区加入新的玩家需要通知游戏后端
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
    public function declineInvitation(Request $request, CommunityInvitationApplication $application)
    {
        if ((int)$application->status !== 0) {
            throw new ApiException('此条申请已被审批');
        }

        $application->status = 2;   //更新申请状态为已拒绝
        $application->save();

        return [
            'code' => -1,
            'data' => '操作成功',
        ];
    }
}
