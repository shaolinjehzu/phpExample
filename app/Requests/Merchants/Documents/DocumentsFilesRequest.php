<?php

namespace App\Http\Requests\Merchants\Documents;

use Illuminate\Foundation\Http\FormRequest;

class DocumentsFilesRequest extends FormRequest
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
            'data.items' => 'required|array|min:1',
            'items.*.document_id' => 'required|integer|exists:merchants_documents,id',
            'items.*.created_by' => 'required|integer|exists:users,id',
            'items.*.url' => 'required|string|max:255',
            'items.*.mime' => 'required|string|max:255',
            'items.*.filename' => 'required|string|max:255',
            'data.request' => 'required|array',
            'request.document_date' => 'nullable|date'
        ];
        switch ($this->getMethod()) {
            case 'POST':
                return $rules;
            case 'PUT':
                return $rules; // и берем все остальные правила
                // case 'PATCH':
            case 'DELETE':
                return [
                    'id' => 'required|exists:documents_files,id'
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
                $data['id'] =  $this->route('file');
        }
        return $data;
    }
}
