<?php

namespace Kutluturkan\ToDoPlanning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToDoPlanningController extends Controller
{
    public function index(Request $req)
    {
        return view('todoplanning::todo-planning-show');
    }
}
