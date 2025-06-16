<?php

namespace App\Enums;

enum ScoreType : string
{
    case SHORT_TEST = 'short_test';
    case LONG_TEST = 'long_test';
    case MIDTERM = 'midterm';
    case FINAL = 'final';
    case OTHER = 'other';
}
