<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = [
        'id',
        'nome'
    ];

    protected $table = 'pessoas';

    public function telefone()
    {
        return $this->hasMany(telefone::class, 'pessoa_id');
    }
}
