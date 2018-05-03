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

    //同意加入群
    public function approveInvitation(Request $request, CommunityInvitationApplication $invitation)
    {
        if ((int)$invitation->status !== 0) {
            throw new ApiException('此条申请已被审批');
        }

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
        if ((int)$invitation->status !== 0) {
            throw new ApiException('此条申请已被审批');
        }

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
            throw new ApiException('此玩家不存在与该牌艺馆，无法退出');
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
}
