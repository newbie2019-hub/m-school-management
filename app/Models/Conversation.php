<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function latest()
    {
        return $this->hasOne(Message::class, 'conversation_id', 'id')->latest();
    }

    public function one_userable()
    {
        return $this->morphTo();
    }

    public function two_userable()
    {
        return $this->morphTo();
    }
}
