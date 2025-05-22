<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
     protected $fillable = ['name','strain','start_date','source','initial_count'];
    public function mortalities()    { return $this->hasMany(Mortalities::class); }
    public function feed(){ return $this->hasMany(Feed::class); }
}
