<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $id)
 * @method static create(array $array)
 */
class Message extends Model
{
    protected $fillable = ['sender_id','receiver_id','body','read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id');
    }
}
