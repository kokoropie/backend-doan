<?php

namespace App\Models;

use App\Models\Traits\UseTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotification extends Model
{
    use SoftDeletes, UseTimestamps;

    protected $table = 'user_notification';

    protected $fillable = [
        'user_id',
        'notification_id',
        'is_read',
    ];

    protected function casts()
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function sender()
    {
        return $this->hasOneThrough(
            User::class,
            Notification::class,
            'id',
            'id',
            'notification_id',
            'sender_id'
        );
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }
}
