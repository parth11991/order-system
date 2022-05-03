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

class orders extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku', 'item_title', 'currency', 'old_price_currency','new_price_currency', 'price', 'new_price', 'old_price','order_date', 'due_date', 'image', 'qty', 'supplier_id','company_id', 'created_by', 'updated_by',
    ];

    /**
     * Get the linnworks that owns the user.
     */
    public function supplier()
    {
        return $this->belongsTo(User::class,'supplier_id');   
    }

    /**
     * Get the linnworks that owns the user.
     */
    public function company()
    {
        return $this->belongsTo(User::class,'company_id');   
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

    /**
     * The users permission that belong to the order.
     */
    public function users_permission()
    {
        return $this->belongsToMany(User::class, 'user_has_orders', 'order_id','user_id');
    }
}
