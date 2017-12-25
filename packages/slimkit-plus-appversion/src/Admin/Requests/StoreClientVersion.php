<?php

namespace Slimkit\PlusAppversion\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientVersion extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(['android', 'ios'])
            ],
            'version' => ['required', 'string'],
            'description' => ['required', 'string'],
            'link' => ['required', 'string'],
            'version_code' => ['required', 'int'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'type' => trans('plus-appversion.attributes.type'),
            'version' => trans('plus-appversion.attributes.version'),
            'description' => trans('plus-appversion.attributes.description'),
            'link' => trans('plus-appversion.attributes.link'),                                 
        ];
    }
}
