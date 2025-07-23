<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class ReportImage extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = 'report_images';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    // protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    // public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    // protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_id', 'image_path'];

    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    // protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getImagePathAttribute($value)
    {
        if ($value) {
            return Storage::disk('public')->url($value);
        }
        return null;
    }

    public function setImagePathAttribute($value)
    {
        $newValue = $value;

        if (isset($value)) {
            // If the value is a file, store it and set the path
            if (is_file($value)) {
                $newValue = $value->store('images/reports', 'public');
            } elseif (is_string($value) && !empty($value)) {
                // If it's a string, assume it's a path and use it directly
                $newValue = $value;
            } else {
                // If the value is not valid, set it to null
                $newValue = null;
            }
        }

        $this->attributes['image_path'] = $newValue;
    }
}
