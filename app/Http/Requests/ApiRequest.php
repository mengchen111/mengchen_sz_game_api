<?php

namespace App\Http\Requests;

use App\Services\ApiAuthService;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ApiAuthService::auth($this->all());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'api_key' => 'required',
            'sign' => 'required',
        ];
    }

    //覆盖ValidatesWhenResolvedTrait的源码的同名方法，先validation再authorization
    public function validate()
    {
        $this->prepareForValidation();

        $instance = $this->getValidatorInstance();

        if (! $instance->passes()) {
            $this->failedValidation($instance);
        } elseif (! $this->passesAuthorization()) {
            $this->failedAuthorization();
        }
    }
}
