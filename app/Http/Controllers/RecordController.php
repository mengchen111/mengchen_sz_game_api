<?php

namespace App\Http\Controllers;

use App\Models\Players;
use App\Models\RecordInfos;
use App\Models\RecordRelative;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function show()
    {
        return Players::with(['records.infos'])->get();
    }
}