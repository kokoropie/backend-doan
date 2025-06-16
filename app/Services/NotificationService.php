<?php
namespace App\Services;

use App\Models\Notification;

class NotificationService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(Notification::class);
    }
}