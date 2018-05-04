<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\ApiLog;

/**
 * @SWG\Swagger(
 *     host=L5_SWAGGER_CONST_HOST,
 *     schemes={"http"},
 *     consumes={"application/json"},
 *
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="梦晨深圳api",
 *         description="梦晨深圳api接口文档",
 *         @SWG\Contact(name="Dian"),
 *     ),
 *
 *     @SWG\Definition(
 *         definition="Code",
 *         type="object",
 *         @SWG\Property(
 *             property="code",
 *             description="返回码，成功为-1",
 *             type="integer",
 *             format="int32",
 *             default=-1,
 *             example=-1,
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="Success",
 *         type="object",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/Code"),
 *         },
 *         @SWG\Property(
 *             property="data",
 *             description="操作消息 or 数据",
 *             type="string",
 *             example="操作成功",
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="ValidationError",
 *         description="表单数据验证错误",
 *         type="object",
 *         @SWG\Property(
 *             property="result",
 *             description="结果(false)",
 *             type="boolean",
 *             default="false",
 *             example="false",
 *         ),
 *         @SWG\Property(
 *             property="code",
 *             description="返回码(0)",
 *             type="integer",
 *             format="int32",
 *             default=0,
 *             example=0,
 *         ),
 *         @SWG\Property(
 *             property="errorMsg",
 *             description="验证错误消息",
 *             type="object",
 *             ref="#/definitions/ValidationErrorDetails",
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="ValidationErrorDetails",
 *         description="key为验证失败的参数名, 值为所有验证失败的条目(数组)",
 *         type="object",
 *         @SWG\Property(
 *             property="name",
 *             example={"name 不能大于 1 个字符", "name 应该为字母"},
 *             type="array",
 *             @SWG\Items(
 *                 type="string",
 *                 description="参数验证失败详情",
 *             ),
 *         ),
 *     ),
 *     @SWG\Definition(
 *         definition="CreatedAtUpdatedAt",
 *         type="object",
 *         @SWG\Property(
 *             property="created_at",
 *             description="创建时间",
 *             type="string",
 *             example="2018-03-30 16:03:14",
 *         ),
 *         @SWG\Property(
 *             property="updated_at",
 *             description="更新时间",
 *             type="string",
 *             example="2018-03-30 17:14:42",
 *         ),
 *     ),
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(Request $request)
    {
        ApiLog::add($request);
    }

    public function res($data = '')
    {
        return [
            'code' => -1,
            'data' => $data,
        ];
    }
}
