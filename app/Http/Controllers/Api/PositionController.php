<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;

class PositionController extends Controller
{

    public function index()
    {
        $positions = Position::all()->setHidden(['created_at', 'updated_at']);

        return [
            'success' => true,
            'positions' => $positions
        ];
    }
}
