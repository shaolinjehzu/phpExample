<?php

namespace App\Http\Controllers\Api\Merchants\Documents;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchant\Documents\Documents;
use App\Http\Requests\Merchants\Documents\DocumentsRequest;
use App\Repositories\Merchants\Documents\DocumentsRepository;

class DocumentsController extends Controller
{
    // space that we can use the repository from
    protected $model;

    public function __construct(Documents $document)
    {
        // set the model
        $this->model = new DocumentsRepository($document);
    }

    public function index()
    {
        return $this->model->all();
    }

    public function filterMethodForMerchant($id)
    {
        return $this->model->filterMethodForMerchant($id);
    }

    public function filterMethodForAdmin($id)
    {
        return $this->model->filterMethodForAdmin($id);
    }

    public function filterMethodForMerchantAll($id)
    {
        return $this->model->filterMethodForMerchantAll($id);
    }

    public function store(DocumentsRequest $request)
    {

        // create record and pass in only fields that are fillable
        return $this->model->create($request->only($this->model->getModel()->fillable));
    }

    public function show($id)
    {
        return $this->model->show($id);
    }

    public function update(DocumentsRequest $request, $id)
    {
        if ($request->is_upload) {
            throw new Exception('Требования запроса уже удовлетворены партнером! Документы загружены!');
        }
        // update model and only pass in the fillable fields
        $this->model->update($request->only($this->model->getModel()->fillable), $id);

        return $this->model->show($id);
    }

    public function destroy(DocumentsRequest $request, $id)
    {
        return $this->model->delete($id);
    }
}
