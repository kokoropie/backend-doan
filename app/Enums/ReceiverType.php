<?php

namespace App\Enums;

enum ReceiverType : string
{
    case ALL = 'all';
    case _CLASS = 'class';
    case TEACHER = 'teacher';
    case STUDENT = 'student';
    case PARENT = 'parent';
}
