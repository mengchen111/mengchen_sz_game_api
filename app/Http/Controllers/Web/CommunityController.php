<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Models\Players;
use App\Models\Web\CommunityList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityController extends Controller
{
    /**
     * 获取此玩家有关联的社区id
     * @SWG\Get(
     *     path="/game/community/involved/{player}",
     *     description="获取此玩家有关联的社区id",
     *     operationId="community.involved.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="player",
     *         description="用户id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="关联的社区",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="owned_communities",
     *                     description="玩家所拥有的的社团",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="integer",
     *                     ),
     *                     example={123123,123123,2222},
     *                 ),
     *                  @SWG\Property(
     *                     property="belongs_to_communities",
     *                     description="玩家所属的社团",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="integer",
     *                     ),
     *                     example={123123,123123,2222},
     *                 ),
     *                  @SWG\Property(
     *                     property="involved_communities",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="object",
     *                         allOf={
     *                             @SWG\Schema(ref="#/definitions/WebCommunityWithOwnerPlayer"),
     *                         },
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
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

    /**
     * 获取社团信息
     * @SWG\Get(
     *     path="/game/community/info/{communityId}",
     *     description="获取社团信息",
     *     operationId="community.info.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="communityId",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="社团信息",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="object",
     *                 allOf={
     *                     @SWG\Schema(ref="#/definitions/WebCommunity"),
     *                 },
     *                 @SWG\Property(
     *                     property="owner_player",
     *                     description="社团所有者玩家信息",
     *                     type="object",
     *                     allOf={
     *                         @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                     },
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
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

    /**
     * 编辑社团信息
     * @SWG\Post(
     *     path="/game/community/info/{communityId}",
     *     description="编辑社团信息",
     *     operationId="community.info.put",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="communityId",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="community_name",
     *         description="社团名字",
     *         in="formData",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="community_info",
     *         description="社团简介",
     *         in="formData",
     *         required=true,
     *         type="string",
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
     *         description="名称重复",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/ApiError"),
     *             },
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="未找到牌艺馆",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="编辑成功",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Success"),
     *             },
     *         ),
     *     ),
     * )
     */
    public function editCommunityInfo(Request $request, CommunityList $community)
    {
        $this->validate($request, [
            'community_name' => 'required|string|max:12',
            'community_info' => 'required|string|max:255',
        ]);

        if ($community->ifNameDuplicated($request->community_name)) {
            throw new ApiException('牌艺馆名称重复');
        } else {
            $community->update([
                'name' => $request->community_name,
                'info' => $request->community_info,
            ]);

            return $this->res('更新牌艺馆信息成功');
        }
    }

    /**
     * 获取牌艺馆入馆申请记录
     * @SWG\Get(
     *     path="/game/community/applications/{community}",
     *     description="获取牌艺馆入馆申请记录",
     *     operationId="community.applications.get",
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
     *         description="申请记录",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="application_count",
     *                     description="申请数量",
     *                     type="integer",
     *                     format="int32",
     *                     example=2,
     *                 ),
     *                 @SWG\Property(
     *                     property="applications",
     *                     description="申请记录",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="object",
     *                         allOf={
     *                             @SWG\Schema(ref="#/definitions/WebCommunityInvitationApplication"),
     *                         },
     *                         @SWG\Property(
     *                             property="player",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/WebCommunityMemberInfo"),
     *                             },
     *                         ),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function getApplications(Request $request, CommunityList $community)
    {
        return $this->res($community->application_data);
    }
}