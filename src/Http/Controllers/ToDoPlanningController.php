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
        try {
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
                $toDoData = $shareToDoList->get();
            } else {
                $toDoData = $this->getCollectionPattern();
            }

            return view('todoplanning::todo-planning-show', ['data' => $toDoData]);
        } catch (\Exception $e) {
            return "The program could not be started !";
        }
    }

    private function getCollectionPattern()
    {
        return  [
            'avarage' => 0,
            'max_time' => 0,
            'min_week' => [
                'weeks' => 0,
                'hours' => 0
            ],
            'tasks_planning' => 0
        ];
    }
}
