<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
class ConnectedDevice extends Model
{
    protected $table = 'connected_device';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
