<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnquiry extends Model
{
    protected $fillable = [
        'course_id',
        'api_course_id',
        'course_title',
        'name',
        'designation',
        'email',
        'phone',
        'school',
        'city',
        'address',
        'message',
        'source',
        'status',
    ];
}
