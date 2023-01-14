<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mclass extends Model
{
    use HasFactory;

    public static $colors = [
        'blue',
        'green',
        'pink',
        'orange',
        'cyan',
        'purple',
        'lightblue',
        'grey',
    ];

    protected $fillable = [
        'name',
        'subject',
        'description',
        'teacher',
        'class_color',
        'code'
    ];


    public function students()
    {
        return $this->hasMany(MclassStudent::class, 'mclass_id', 'id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher', 'id');
    }
}
