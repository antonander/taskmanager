<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskManager extends Model
{
    protected $table = 'tasks';

    public $timestamps = false;

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

}
