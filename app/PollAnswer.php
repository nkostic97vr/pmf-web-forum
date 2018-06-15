<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollAnswer extends Model
{
    public $timestamps = false;

    public function poll()
    {
        return $this->belongsTo('poll');
    }
}
