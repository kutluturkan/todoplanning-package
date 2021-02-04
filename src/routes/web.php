<?php

use Illuminate\Support\Facades\Route;
use Kutluturkan\ToDoPlanning\Http\Controllers\ToDoPlanningController;

Route::get('/', [ToDoPlanningController::class, 'index']);
