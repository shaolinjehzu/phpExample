<?php

namespace App\Models\Merchant\Documents;

use App\Models\User;
use App\Models\Merchant\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Merchant\Documents\DocumentType;


class Documents extends Model
{
    use SoftDeletes;

    protected $table = 'merchants_documents';

    protected $fillable = [
        'number',
        'merchant_id',
        'document_type_id',
        'user_id',
        'document_date',
        'is_upload',
        'confirmed'
    ];

    public function merchants()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id')->select('id', 'name');
    }

    public function documents_type()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id')->select('id', 'type_title', 'description');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name', 'lastname', 'middlename');
    }

    public function documents()
    {
        return $this->hasMany(DocumentsFiles::class, 'document_id', 'id');
    }

    public function examples()
    {
        return $this->hasMany(ExamplesDocuments::class, 'document_type_id', 'document_type_id');
    }
}
