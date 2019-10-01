<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addressbook extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address'
    ];
    
    public function user()
	{
	    return $this->belongsTo('App\User');
	}
}
