<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RoboquestConnection extends Model {
    protected $guarded = ['id'];
    protected $hidden = ['shared_key'];
    protected $casts = ['shared_key' => 'encrypted'];
}
