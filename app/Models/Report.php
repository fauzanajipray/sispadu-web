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
    const SUCCESS = 'completed';
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

    public function createStatusLog($user_id, $to_status, $note, $position_id = null, $disposition_id = null)
    {
        $this->statusLogs()->create([
            'user_id' => $user_id,
            'from_status' => $this->status,
            'position_id' => $position_id, 
            'to_status' => $to_status,
            'disposition_id' => $disposition_id,
            'note' => $note,
        ]);
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

    public function position()
    {
        return $this->belongsTo(Position::class, 'temp_position_id');
    }

    public function images()
    {
        return $this->hasMany(ReportImage::class, 'report_id');
    }

    public function dispositions()
    {
        return $this->hasMany(ReportDisposition::class, 'report_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(ReportStatusLog::class, 'report_id');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(ReportStatusLog::class)->latestOfMany();
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
