<?php

namespace App\Http\Controllers\Api\Merchants\Documents;

use Exception;
use App\Http\Controllers\Controller;
use App\Models\Merchant\Documents\Documents;
use Symfony\Component\HttpFoundation\Request;
use App\Models\Merchant\Documents\DocumentsFiles;
use App\Repositories\Merchants\Documents\DocumentsRepository;
use App\Http\Requests\Merchants\Documents\DocumentsFilesRequest;
use App\Notifications\Merchants\MerchantsDocumentsNotifications;
use App\Repositories\Merchants\Documents\DocumentsFilesRepository;

class DocumentsFilesController extends Controller
{
    // space that we can use the repository from
    protected $model;
    protected $document;

    public function __construct(DocumentsFiles $documentFile, Documents $document)
    {
        // set the model
        $this->model = new DocumentsFilesRepository($documentFile);
        $this->document = new DocumentsRepository($document);
    }

    public function index()
    {
        return $this->model->all();
    }

    public function store(DocumentsFilesRequest $request)
    {
        // create record and pass in only fields that are fillable

        $files = $this->model->create($request['data']['items']);
        if ($files) {
            $data = $request['data']['request'];
            $documents = $this->document->update($data, $data['document_id']);
        }
        if ($files && $documents) {
            return 'Success!';
        } else {
            throw new Exception('Ошибка записи данных!');
        }
    }

    public function show($id)
    {
        return $this->model->show($id);
    }

    public function update(DocumentsFilesRequest $request, $id)
    {
        // update model and only pass in the fillable fields
        $this->model->update($request->only($this->model->getModel()->fillable), $id);

        return $this->model->show($id);
    }

    public function destroy(DocumentsFilesRequest $request, $id)
    {
        $this->deleteBeforeUpload($request);
        $file = $this->show($id);
        $this->document->update(['document_date' => NULL, 'is_upload' => false], $file->documents['id']);
        return $this->model->delete($id);
    }

    public function upload(Request $request)
    {
        return $this->model->upload($request);
    }

    public function deleteBeforeUpload(Request $request)
    {
        return $this->model->deleteBeforeUpload($request->url);
    }

    public function downloadFile($id)
    {
        return $this->model->downloadFile($id);
    }
}
