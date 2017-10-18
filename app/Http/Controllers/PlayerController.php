<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\ApiRequest;
use App\Models\Players;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Request;
use Exception;

class PlayerController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $players = Players::all();
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }

    }

    public function search(ApiRequest $request)
    {
        $searchUid = $this->filterRequest($request);

        try {
            $players = Players::where('id', 'like', "%${searchUid}%")->get();
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    protected function filterRequest($request)
    {
        $this->validate($request, [
            'uid' => 'required|numeric',
        ]);
        return $request->uid;
    }
}
