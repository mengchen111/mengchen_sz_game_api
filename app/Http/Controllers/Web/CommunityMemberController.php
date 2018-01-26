<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Models\Web\CommunityList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web\CommunityInvitationApplication;

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

        $this->checkIfDuplicationApplication($playerId, $communityId);
        $this->checkIfInTheCommunity($playerId, $communityId);

        CommunityInvitationApplication::create([
            'player_id' => $playerId,
            'community_id' => $communityId,
            'status' => 0,
            'type' => 0
        ]);

        return [
            'code' => -1,
            'message' => '申请成功',
        ];
    }

    protected function checkIfDuplicationApplication($playerId, $communityId)
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
    }
}
