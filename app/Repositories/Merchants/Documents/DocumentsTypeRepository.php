<?php

namespace App\Repositories\Merchants\Documents;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\RepositoryInterface;

class DocumentsTypeRepository implements RepositoryInterface
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
        return $this->with(['examples'])->get();
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
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
