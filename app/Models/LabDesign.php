<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabDesign extends Model
{
    protected $table = 'lab_designs';

    protected $fillable = [
        'session_id',
        'reference',
        'source',
        'status',
        'session_url',
        'result',
        'name',
        'school',
        'email',
        'phone',
    ];

    protected $casts = [
        'result' => 'array',
    ];
}
