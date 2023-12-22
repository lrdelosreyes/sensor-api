<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sensor_id',
        'reading_value',
        'unit'
    ];

     /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'logged_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
