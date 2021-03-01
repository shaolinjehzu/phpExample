<?php

namespace App\Http\Controllers\Api\Merchants\Documents;





use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchant\Documents\Documents;
use App\Models\Merchant\Documents\ExamplesDocuments;
use App\Repositories\Merchants\Documents\DocumentsRepository;
use App\Http\Requests\Merchants\Documents\ExamplesDocumentsRequest;
use App\Repositories\Merchants\Documents\ExamplesDocumentsRepository;

class ExamplesDocumentsController extends Controller
{
    // space that we can use the repository from
    protected $model;
    protected $document;

    public function __construct(ExamplesDocuments $exampleDocument, Documents $document)
    {
        // set the model
        $this->model = new ExamplesDocumentsRepository($exampleDocument);
        $this->document = new DocumentsRepository($document);
    }

    public function index()
    {
        return $this->model->all();
    }

    public function store(ExamplesDocumentsRequest $request)
    {
        // create record and pass in only fields that are fillable
        return $this->model->create($request->only($this->model->getModel()->fillable));
    }

    public function show(ExamplesDocumentsRequest $id)
    {
        return $this->model->show($id);
    }

    public function update(ExamplesDocumentsRequest $request, $id)
    {
        // update model and only pass in the fillable fields
        $this->model->update($request->only($this->model->getModel()->fillable), $id);

        return $this->model->show($id);
    }

    public function destroy(ExamplesDocumentsRequest $request, $id)
    {
        return $this->model->delete($id);
    }

    public function upload(Request $request)
    {
        return $this->model->upload($request);
    }

    public function downloadFile(Request $request, $id)
    {
        if ($request->merchant_id) {
            $this->document->create($request->only($this->document->getModel()->fillable));
        }
        return $this->model->downloadFile($id);
    }
}
