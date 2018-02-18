<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'domain_id'];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
