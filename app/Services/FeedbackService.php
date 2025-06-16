<?php
namespace App\Services;

use App\Models\Feedback;

class FeedbackService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setModel(Feedback::class);
    }
}