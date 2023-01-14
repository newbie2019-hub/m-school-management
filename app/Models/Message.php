<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'to_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'from_id', 'id');
    }
}
