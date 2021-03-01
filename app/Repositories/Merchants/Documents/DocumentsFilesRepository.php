<?php

namespace App\Repositories\Merchants\Documents;

use Exception;
use App\Services\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Response;

class DocumentsFilesRepository implements RepositoryInterface
{
    // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->with(['documents', 'user'])->get();
    }

    // create a new record in the database
    public function create(array $data)
    {
        try {
            $this->model->insert($data);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->show($id);
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        if ($this->model->destroy($id)) {
            return $id;
        } else {
            throw new Exception('Ошибка удаления документа');
        }
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->model->findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        return $this->model->with($relations);
    }

    public function upload($array)
    {
        /*         if ($array->url) {
            $this->deleteBeforeUpload($array->url);
        } */
        $file = $array->file('file');
        $filename = $file->getClientOriginalName();
        $mime = $file->getClientMimeType();
        $url = Storage::disk('yandex')->put('/merchants_documents', $array->file('file'));
        $this->deleteBeforeUpload($array->url);
        /* $fileParams = File::delete($url);
        if (isset($fileParams['error'])) {
            throw new Exception($fileParams['error']);
        } */
        return [
            'url' => $url,
            'mime' => $mime,
            'filename' => $filename
        ];
    }

    public function deleteBeforeUpload($url)
    {
        $fileParams = File::delete($url);
        if (isset($fileParams['error'])) {
            throw new Exception($fileParams['error']);
        }
    }

    public function downloadFile($id)
    {

        $file = $this->show($id);
        $mime = Storage::disk('yandex')->getDriver()->getMimetype($file['url']);
        $size = Storage::disk('yandex')->getDriver()->getSize($file['url']);

        $response =  [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Content-Description' => 'File Transfer',
            'Content-Disposition' => "attachment; filename={$file['filename']}",
            'Content-Transfer-Encoding' => 'binary',
        ];



        return Response::make(Storage::disk('yandex')->get($file['url']), 200, $response);
    }
}
