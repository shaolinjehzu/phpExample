<?php

namespace App\Repositories\Merchants\Documents;

use App\Facades\Auth;
use App\Models\Merchant\Documents\DocumentType;
use Exception;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Merchants\MerchantsOrdersNotifications;
use App\Notifications\Merchants\MerchantsDocumentsNotifications;

class DocumentsRepository implements RepositoryInterface
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
        return $this->with(['merchants', 'documents_type', 'user', 'documents'])->get();
    }

    public function filterMethodForMerchant($merchant_id)
    {
        return $this->with(['merchants', 'documents_type', 'user', 'documents', 'examples'])->where(['merchant_id' => $merchant_id, 'is_upload' => false])->get();
    }

    public function filterMethodForMerchantAll($merchant_id)
    {
        return $this->with(['documents_type', 'documents'])->where(['merchant_id' => $merchant_id])->get();
    }

    public function filterMethodForAdmin($merchant_id)
    {
        return $this->with(['merchants', 'documents_type', 'user', 'documents'])->where(['merchant_id' => $merchant_id])->get();
    }

    // create a new record in the database
    public function create(array $data)
    {

        $exist = $this->model->where(['merchant_id' => $data['merchant_id'], 'document_type_id' => $data['document_type_id']])->first();
        if ($exist) {
            throw New Exception('Требование этого типа документа уже было выставлено ранее!');
        } else {

            $last =  $this->model->latest()->first();
            $id = $last['id'] ? $last['id'] + 1 : 1;
            $data['number'] = date("Y") . '-' . $data['document_type_id'] . '-' . $id;
            $data['user_id'] = Auth::id();
            $merchant = Merchant::findOrFail($data['merchant_id']);
            if ($data['document_type_id'] == '1') {
                $merchant->contract_number = $data['number'];
                $merchant->save();
            };
            $type = DocumentType::findOrFail($data['document_type_id']);
            $message = [
                'title' => 'Требование загрузить документы',
                'body' => 'Требование загрузить ' . $type['type_title']
            ];


            Notification::send($merchant, new MerchantsDocumentsNotifications($message['title'], $message['body']));


            $this->model->create($data);
            return $this->with(['merchants', 'documents_type', 'user', 'documents'])->latest()->first();
        }
    }

    // update record in the database
    public function update(array $data, $id)
    {
        $record = $this->show($id);
        $merchant = Merchant::findOrFail($record->merchant_id);
        if (isset($data['document_date']) && $data['document_date']) {
            $merchant->contract_date = $data['document_date'];
            $merchant->save();
        }
        Notification::send($merchant, new MerchantsDocumentsNotifications('Требование на загрузку документов изменено!', 'Требование в разделе юридических документов было отредактировано администрацией!'));
        return $record->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        if ($this->model->destroy($id)) {
            return $id;
        } else {
            throw new Exception('Ошибка удаления типа');
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
}
