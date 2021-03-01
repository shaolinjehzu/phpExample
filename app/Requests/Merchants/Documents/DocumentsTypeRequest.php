<?php

namespace App\Http\Requests\Merchants\Documents;

use Illuminate\Foundation\Http\FormRequest;

class DocumentsTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'type_title' => 'required|string|unique:documents_type|max:255',
            'description' => 'required|string|max:255'
        ];
        switch ($this->getMethod()) {
            case 'POST':
                return $rules;
            case 'PUT':
                return $rules;
            case 'PATCH':
                return [
                    'type_title' => 'string|unique:documents_type|max:255',
                    'description' => 'string|max:255',
                    'item_id' => 'required|integer|exists:documents_type,id'
                ] + $rules;
            case 'DELETE':
                return [
                    'id' => 'required|integer|exists:documents_type,id'
                ];
        }
    }

    public function all($keys = null)
    {
        // return $this->all();
        $data = parent::all($keys);
        switch ($this->getMethod()) {
                // case 'PUT':
            case 'PATCH':
                $data = parent::all($keys);
                $data['item_id'] = $this->route('type');
            case 'DELETE':
                $data['id'] =  $this->route('type');
        }
        return $data;
    }
}
