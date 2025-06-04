<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'reports';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    const SUBMITTED = 'submitted';
    const PENDING = 'pending';
    const SUCCESS = 'success';
    const REJECTED = 'rejected';
    const CANCELLED = 'cancelled';


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();

        // Event triggered after a report is created
        static::created(function ($report) {
            // Create a new ReportStatusLog entry
            \App\Models\ReportStatusLog::create([
                'report_id' => $report->id,
                'user_id' => $report->user_id, // Optional: user who created the report
                'from_status' => null,
                'to_status' => self::SUBMITTED,
                'note' => 'Report created with status Submitted',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class, 'report_id');
    }

    public function dispositions()
    {
        return $this->hasMany(ReportDisposition::class, 'report_id');
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
}
