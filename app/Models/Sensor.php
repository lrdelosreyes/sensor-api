<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'lat',
        'long'
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
     protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function readings()
    {
        return $this->hasMany(Reading::class, 'sensor_id');
    }

    public function reading()
    {
        return $this->readings()->one()->ofMany('logged_at', 'max');
    }
}
