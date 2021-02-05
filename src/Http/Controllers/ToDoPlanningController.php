<?php

namespace Kutluturkan\ToDoPlanning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kutluturkan\ToDoPlanning\Helpers\ShareToDoList;
use Kutluturkan\ToDoPlanning\Models\ToDoList;

class ToDoPlanningController extends Controller
{
    public function index(Request $req)
    {
        //Get to do list from DB
        $toDoList = new ToDoList;
        $toDoData = $toDoList->getToDoListShorting();

        //Get max job time
        $maxDurationTime = $toDoList->maxDurationTime();

        //Get developers from config
        $developers = config('todoplanning.developers');

        if ($maxDurationTime) {

            //Share all tasks with staff
            $shareToDoList = new ShareToDoList($toDoData, $developers, $maxDurationTime->estimated_duration);
            print_r($shareToDoList->get());
            exit;
        }

        return view('todoplanning::todo-planning-show');
    }
}
