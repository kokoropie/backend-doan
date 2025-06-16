<?php

namespace App\Enums;

enum FeedbackStatus : string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
}
