<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sku', 'created_at', 'updated_at'
    ];

    /**
     * The users that belong to the branch.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'supplier_has_items', 'item_id','user_id');
    }
}
