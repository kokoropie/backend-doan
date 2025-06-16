<?php

namespace App\Models;

use App\Enums\ReceiverType;
use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes, HasFactory, UseTimestamps;

    protected $table = 'notifications';

    protected $fillable = [
        'title',
        'message',
        'receiver_type',
        'sender_id',
    ];

    protected function casts()
    {
        return [
            'receiver_type' => ReceiverType::class,
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receivers()
    {
        return $this->hasManyThrough(
            User::class,
            UserNotification::class,
            'notification_id',
            'id',
            'id',
            'user_id'
        );
    }
}
