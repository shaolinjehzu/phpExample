<?php

namespace App\Models\Merchant\Documents;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use App\Models\Merchant\Documents\Documents;


class DocumentsFiles extends Model
{

    protected $table = 'documents_files';

    protected $fillable = [
        'document_id',
        'filename',
        'mime',
        'created_by',
    ];

    public function documents()
    {
        return $this->belongsTo(Documents::class, 'document_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name', 'lastname', 'middlename');
    }
}
