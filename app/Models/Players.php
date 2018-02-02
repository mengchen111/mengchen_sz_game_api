<?php

namespace App\Models;

use App\Models\Web\CommunityList;
use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    public $timestamps = false;
    public $connection = 'mysql';
    protected $table = 'account';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        'permissions',
    ];

    protected $appends = [
        'openid',
    ];

//    public function records()
//    {
//        return $this->hasMany('App\Models\RecordRelative', 'uid', 'id');
//    }

    public function getRecords()
    {
        $records = RecordRelative::with('infos')->where('uid', $this->id)->get()->toArray();
        $recordsNew = RecordRelativeNew::with('infos')->where('uid', $this->id)->get()->toArray();
        return array_merge($records, $recordsNew);
        //return $records;
    }

    public function getOpenidAttribute()
    {
        $unionidOpenid = UnionidOpenid::find($this->attributes['unionid']);
        return empty($unionidOpenid) ? null : $unionidOpenid->openid;
    }

    //获取玩家所拥有的的社团
    public function getOwnedCommunities()
    {
        return CommunityList::where('owner_player_id', $this->id)
            ->where('status', 1)    //审核已通过的社区
            ->get()
            ->pluck('id')
            ->toArray();
    }

    //获取玩家所属的社团（为社团成员，社团长不算）
    public function getBelongs2Communities()
    {
        $belongs2Communities = [];
        $communities = CommunityList::where('status', 1)
            ->get()
            ->each(function ($item) {
                $item->append('member_ids');
            });
        foreach ($communities as $community) {
            if (in_array($this->id, $community->member_ids)) {
                array_push($belongs2Communities, $community->id);
            }
        }
        return $belongs2Communities;
    }

    public function getInvolvedCommunities()
    {
        $data['owned_communities'] = $this->getOwnedCommunities();
        $data['belongs_to_communities'] = $this->getBelongs2Communities();
        $data['involved_communities'] = array_unique(array_merge($data['owned_communities'],
            $data['belongs_to_communities']));
        return $data;
    }
}
