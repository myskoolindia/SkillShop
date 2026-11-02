<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class RoboquestConnection extends Model {
    protected $guarded = ['id'];
    protected $hidden = ['shared_key'];

    public function getSharedKeyAttribute($value) {
        if (empty($value)) return $value;
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    public function setSharedKeyAttribute($value) {
        if (empty($value)) {
            $this->attributes['shared_key'] = $value;
        } else {
            try {
                Crypt::decryptString($value);
                $this->attributes['shared_key'] = $value;
            } catch (\Throwable $e) {
                $this->attributes['shared_key'] = Crypt::encryptString($value);
            }
        }
    }
}
