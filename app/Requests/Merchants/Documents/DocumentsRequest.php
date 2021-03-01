<?php

namespace App\Http\Requests\Merchants\Documents;

use Illuminate\Foundation\Http\FormRequest;

class DocumentsRequest extends FormRequest
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
            'merchant_id' => 'required|integer|exists:merchants,id',
            'document_type_id' => 'required|integer|exists:documents_type,id'
        ];
        switch ($this->getMethod()) {
            case 'POST':
                return $rules;
            case 'PUT':
                return  $rules; // и берем все остальные правила
                // case 'PATCH':
            case 'DELETE':
                return [
                    'id' => 'required|integer|exists:merchants_documents,id'
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
                $data['id'] =  $this->route('request');
        }
        return $data;
    }
}
