<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linnworks extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'applicationId', 'applicationSecret', 'passportAccessToken', 'created_by', 'updated_by',
    ];

    /**
     * Get the linnworks that owns the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');   
    }

    /**
     * Get the creator for the company.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor for the company.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }
}
