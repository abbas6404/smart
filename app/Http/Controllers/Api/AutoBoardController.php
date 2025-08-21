<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutoBoard;

class AutoBoardController extends Controller
{
    /**
     * Get auto board status
     */
    public function status()
    {
        $autoBoard = AutoBoard::first();
        
        return response()->json([
            'auto_board' => $autoBoard
        ]);
    }
}
