<?php

namespace App\Http\Controllers\Api\Merchants\Documents;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Merchant\Documents\DocumentType;
use App\Models\Merchant\Documents\ExamplesDocuments;
use App\Http\Requests\Merchants\Documents\DocumentsTypeRequest;
use App\Repositories\Merchants\Documents\DocumentsTypeRepository;
use App\Repositories\Merchants\Documents\ExamplesDocumentsRepository;

class DocumentsTypeController extends Controller
{
    // space that we can use the repository from
    protected $model;
    protected $example;

    public function __construct(DocumentType $documentType, ExamplesDocuments $exampleDocument)
    {
        // set the model
        $this->model = new DocumentsTypeRepository($documentType);
        $this->example = new ExamplesDocumentsRepository($exampleDocument);
    }

    public function index()
    {
        return $this->model->all();
    }

    public function store(DocumentsTypeRequest $request)
    {

        $type = $this->model->create($request->only($this->model->getModel()->fillable));
        $request['document_type_id'] = $type['id'];
        $request['title'] = $type['type_title'];
        $this->example->create($request->only($this->model->getModel()->fillable));
        // create record and pass in only fields that are fillable
        return $this->show($type['id']);
    }

    public function show(DocumentsTypeRequest $id)
    {
        return $this->model->show($id);
    }

    public function update(DocumentsTypeRequest $request, $id)
    {
        // update model and only pass in the fillable fields
        $type = $this->model->update($request->only($this->model->getModel()->fillable), $id);
        $request['document_type_id'] = $type['id'];
        $request['title'] = $type['type_title'];
        $this->example->update($request->only($this->model->getModel()->fillable), $request['examples'][0]['id']);
        return $this->model->show($id);
    }

    public function destroy(DocumentsTypeRequest $request, $id)
    {
        return $this->model->delete($id);
    }
}
