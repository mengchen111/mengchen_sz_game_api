<?php

namespace App\Http\Controllers\Web;

use App\Models\Players;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityController extends Controller
{
    public function getPlayerInvolvedCommunities(Request $request, Players $player)
    {
        $playerOwnedCommunities = $player->getInvolvedCommunities();

        return [
            'code' =>  -1,
            'data' => $playerOwnedCommunities
        ];
    }
}