<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function count()
    {
        $count = [];

        return response()->success($count, 'Thống kê thành công');
    }
}
