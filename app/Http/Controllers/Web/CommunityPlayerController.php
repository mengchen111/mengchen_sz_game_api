<?php

namespace App\Http\Controllers\Web;

use App\Models\Players;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityPlayerController extends Controller
{
    /**
     * 根据玩家id查询玩家信息
     * @SWG\Get(
     *     path="/game/community/player/search/{player}",
     *     description="根据玩家id查询玩家信息",
     *     operationId="community.player.search.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="player_id",
     *         description="玩家id",
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
     *                         @SWG\Schema(ref="#/definitions/GamePlayerSimplified"),
     *                     }
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @SWG\Response(
     *         response=404,
     *         description="未找到玩家",
     *     ),
     * )
     */
    public function searchPlayer(Request $request, Players $player)
    {
        return $this->res($player->setVisible(['id', 'nickname', 'headimg']));
    }
}
