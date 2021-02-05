<?php

namespace Kutluturkan\ToDoPlanning\Helpers;

class Helper
{
    public static function showWeekAndHours($floatVal)
    {
        $time = [
            'weeks' => 0,
            'hours' => 0
        ];

        if (strpos($floatVal, ".") !== false) {
            $exp = explode(".", $floatVal);
            $time['weeks'] = $exp[0];
            $time['hours'] = $exp[1];
        } else {
            $time['weeks'] = $floatVal;
        }

        return $time;
    }

    public static function shareTaskForWeek($taskData)
    {
        $RESPONSE = [];
        $max_week_hours = config('todoplanning.max_working_time');
        foreach ($taskData as $data_key => $data_val) {
            foreach ($data_val as $dev_key => $dev_val) {
                $week_counter = 1;
                $hours = 0;
                foreach ($dev_val as $task_value) {

                    if ($hours >=  ($max_week_hours * $week_counter)) {
                        $week_counter++;
                    }
                    $RESPONSE[$data_key][$dev_key][$week_counter][] = $task_value;
                    $hours += $task_value['estimated_duration'];
                }
            }
        }

        return $RESPONSE;
    }
}
