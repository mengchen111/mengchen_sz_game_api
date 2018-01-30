<?php

namespace App\Http\Controllers\Web;

use App\Models\Players;
use App\Models\Web\CommunityList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityController extends Controller
{
    public function getPlayerInvolvedCommunities(Request $request, Players $player)
    {
        $playerOwnedCommunities = $player->getInvolvedCommunities();

        $communities = [];
        foreach ($playerOwnedCommunities['involved_communities'] as $communityId) {
            array_push($communities, CommunityList::with('ownerPlayer')->find($communityId)->makeHidden(['members']));
        }
        $playerOwnedCommunities['involved_communities'] = $communities;

        return [
            'code' =>  -1,
            'data' => $playerOwnedCommunities
        ];
    }

    public function getCommunityInfo(Request $request, $communityId)
    {
        $community = CommunityList::with('ownerPlayer')->find($communityId);
        if (! empty($community)) {
            $community->makeHidden(['members']);
        } else {
            $community = [];
        }

        return [
            'code' => -1,
            'data' => $community,
        ];
    }
}