<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersFiles extends Model
{
    use HasFactory;

    /**
     * Get the creator of this order.
     */
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    /**
     * Get the last editor of this order.
     */
    public function editor(){
        return $this->belongsTo(User::class,'updated_by');
    }
}
