<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Livro extends Model
{
    protected $table = 'livros';

    protected $fillable = ['titulo', 'ano_publicacao', 'isbn', 'autor_id'];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class);
    }
}
