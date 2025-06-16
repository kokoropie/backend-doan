<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return response()->success(UserResource::make($this->_USER), 'Get user profile successfully');
    }

    public function children()
    {
        return response()->success(UserResource::collection($this->_USER->children), 'Get children successfully');
    }

    public function parents()
    {
        return response()->success(UserResource::collection($this->_USER->parents), 'Get parents successfully');
    }
}
