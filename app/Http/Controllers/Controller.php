<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected ?User $_USER;

    public function __construct()
    {
        $this->_USER = auth()->user();
    }
}
