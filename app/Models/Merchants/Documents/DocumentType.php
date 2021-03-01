<?php

namespace App\Models\Merchant\Documents;


use Illuminate\Database\Eloquent\Model;


class DocumentType extends Model
{

    protected $table = 'documents_type';

    protected $fillable = [
        'type_title',
        'description'
    ];

    public function examples()
    {
        return $this->hasMany(ExamplesDocuments::class, 'document_type_id');
    }
}
