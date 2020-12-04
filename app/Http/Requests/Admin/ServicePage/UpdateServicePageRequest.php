<?php

namespace App\Http\Requests\Admin\ServicePage;

use App\Libraries\GraphQL\AbstractValidation;

class UpdateServicePageRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id'=> 'required|string',
            'title' => 'string|min:3|unique:service_pages,title',
            'content' => 'string|min:10',
            //'slug' => 'string|min:2|max:30',
            'order' => 'integer|min:0',
            'status' => 'boolean',
        ];
    }
}
