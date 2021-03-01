<?php

namespace App\Http\Requests\Merchants\Documents;

use Illuminate\Foundation\Http\FormRequest;

class ExamplesDocumentsRequest extends FormRequest
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
            'title' => 'required|string|unique:examples_documents|max:255',
            'description' => 'required|string|max:255',
            'filename' => 'required|string|unique:examples_documents|max:255',
            'mime' => 'required|string|max:255',
            'document_type_id' => 'required|integer|exists:documents_type,id',
            'url' => 'required|string|max:255'
        ];
        switch ($this->getMethod()) {
            case 'POST':
                return $rules;
            case 'PUT':
                return [
                    'id' => 'required|integer|exists:examples_documents,id'
                ];
            case 'PATCH':
                return [
                    'id' => 'required|integer|exists:examples_documents,id'
                ];
            case 'DELETE':
                return [
                    'id' => 'required|integer|exists:examples_documents,id'
                ];
        }
    }

    public function all($keys = null)
    {
        // return $this->all();
        $data = parent::all($keys);
        switch ($this->getMethod()) {
            case 'PUT':
                $data = parent::all($keys);
                $data['id'] = $this->route('example');
            case 'PATCH':
                $data = parent::all($keys);
                $data['id'] = $this->route('example');
            case 'DELETE':
                $data['id'] =  $this->route('example');
        }
        return $data;
    }
}
