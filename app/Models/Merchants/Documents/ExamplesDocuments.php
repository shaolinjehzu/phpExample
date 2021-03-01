<?php

namespace App\Models\Merchant\Documents;


use Illuminate\Database\Eloquent\Model;
use App\Models\Merchant\Documents\DocumentType;


class ExamplesDocuments extends Model
{

    protected $table = 'examples_documents';

    protected $fillable = [
        'title',
        'description',
        'filename',
        'mime',
        'url',
        'document_type_id'
    ];

    public function documents_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
