<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CrudTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    // Button

    public function showUpdatePositionButton()
    {
        if ($this->role === 'superadmin') {
            return ''; // Tidak menampilkan tombol jika role adalah superadmin
        }
        
        $buttonText = $this->position_id ? 'Update Jabatan' : 'Tambah Jabatan';

        return '<a href="javascript:void(0);" onclick="showModalForm(' . $this->getKey() . ')" 
        class="btn btn-sm btn-link" title="' . $buttonText . '">
        <i class="la la-user-tie"></i> ' . $buttonText . '</a>';
    }
}
