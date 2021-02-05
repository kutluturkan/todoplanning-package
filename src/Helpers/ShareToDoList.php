<?php

namespace Kutluturkan\ToDoPlanning\Helpers;

class ShareToDoList
{
    private $TASKS, $SHARERING_TASK;
    private $DEVELOPERS;
    private $TOTAL_TASK_TIME, $TOTAL_STAFF;
    private $AVERAGE;
    private $STAFF_TASKS;
    private $MAX_DURATION;
    private $MAX_TOLERANS, $MIN_TOLERANS;
    private $EXCESS_TASK = [];

    public function __construct($taskData, $developers, $maxDurationTime = 0)
    {
        $this->TASKS = $taskData;
        $this->SHARERING_TASK = $taskData;
        $this->DEVELOPERS = $developers;
        $this->MAX_DURATION = intval($maxDurationTime);
        $this->set();
    }

    /**
     * Set require veriable
     *
     * @return void
     */
    private function set()
    {
        $this->TOTAL_TASK_TIME  = $this->getTotalTime();
        $this->TOTAL_STAFF      = $this->getTotalDevelopers();

        if ($this->TOTAL_TASK_TIME > 0 && $this->TOTAL_STAFF > 0) {
            $this->AVERAGE          = ceil($this->TOTAL_TASK_TIME / $this->TOTAL_STAFF);
        } else {
            $this->AVERAGE = $this->TOTAL_TASK_TIME;
        }

        $this->MAX_TOLERANS = intval($this->AVERAGE + $this->MAX_DURATION);
        $this->MIN_TOLERANS = intval($this->AVERAGE - $this->MAX_DURATION);

        $this->shareTasksForEachStaff();
        $this->balancing();
    }

    /**
     * Get settings collection
     *
     * @return array
     */
    public function get()
    {
        $max_time = $this->getMaxJobTime($this->STAFF_TASKS);

        $min_week = null;
        if ($max_time > 0 && config('todoplanning.max_working_time') > 0) {
            $min_week = round($max_time / config('todoplanning.max_working_time'), 1);
        }

        return [
            'avarage' => $this->AVERAGE,
            'max_time' => $max_time,
            'min_week' => $min_week,
            'tasks_planning' => $this->STAFF_TASKS
        ];
        //return $this->TOTAL_TASK_TIME . " / " . $this->AVERAGE;
        //return $this->STAFF_TASKS;
    }

    /**
     * Get max duration time from staff
     *
     * @return int
     */
    public function getMaxJobTime($taskData)
    {
        $maxTime = 0;
        $calcMaxTime = 0;
        for ($i = config('todoplanning.total_level'); $i >= 1; $i--) {
            foreach ($taskData[$i] as $valLevel) {
                $calcMaxTime = 0;
                foreach ($valLevel as $val) {
                    $calcMaxTime += intval($val['estimated_duration']);
                }
                $maxTime = ($calcMaxTime > $maxTime) ? $calcMaxTime : $maxTime;
            }
        }

        return $maxTime;
    }

    /**
     * Return total task time as hour
     *
     * @return int
     */
    private function getTotalTime()
    {
        $totalTime = 0;
        foreach ($this->TASKS as $valLevel) {
            foreach ($valLevel as $val) {
                $totalTime += intval($val['estimated_duration']);
            }
        }
        return $totalTime;
    }

    /**
     * Return total developer
     *
     * @return int
     */
    private function getTotalDevelopers()
    {
        $totalStaff = 0;
        foreach ($this->DEVELOPERS as $val) {
            $totalStaff += intval($val['staff_count']);
        }
        return $totalStaff;
    }

    /**
     * Share task with staff for each level
     *
     * @return void
     */
    private function shareTasksForEachStaff()
    {
        for ($i = config('todoplanning.total_level'); $i >= 1; $i--) {
            $this->setShare($i);
        }
    }

    /**
     * Share task with staff 
     *
     * @return void
     */
    private function setShare($key_number)
    {
        $staffCount = $this->DEVELOPERS[$key_number]['staff_count'];
        $s_counter = 1;
        if (array_key_exists($key_number, $this->SHARERING_TASK) && $staffCount > 0) {
            foreach ($this->SHARERING_TASK[$key_number] as $key => $val) {

                $this->STAFF_TASKS[$key_number][$s_counter][] = $val;
                //Reverse staff
                $s_counter = ($staffCount <= $s_counter++) ? 1 : $s_counter;
            }

            unset($this->SHARERING_TASK[$key_number]);
        }
    }

    /**
     * Balancing 
     *
     * @return void
     */
    private function balancing()
    {
        for ($i = 1; $i <= config('todoplanning.total_level'); $i++) {

            if (array_key_exists($i, $this->STAFF_TASKS)) {

                foreach ($this->STAFF_TASKS[$i] as $key => $val) {

                    $resTaskTime = $this->getStaffTaskTotalTime($val);

                    if ($resTaskTime > $this->MAX_TOLERANS && $i < config('todoplanning.total_level')) {
                        $this->takeTaskFromStaff($i, $key, $val);
                    } elseif ($resTaskTime <= $this->MIN_TOLERANS) {
                        $this->pushTaskToStaff($i, $key, $val);
                    }
                }
            }
        }
    }

    /**
     * Take task from staff for balancing 
     *
     * @return void
     */
    private function takeTaskFromStaff($levelNumber, $staffId, $data)
    {
        if (count($data) > 0) {
            do {
                $keyLast = array_key_last($data);
                $this->EXCESS_TASK[] = $data[$keyLast];
                unset($data[$keyLast]);
            } while ($this->MAX_TOLERANS <= $this->getStaffTaskTotalTime($data) && count($data) > 0);

            $this->STAFF_TASKS[$levelNumber][$staffId] = $data;
        }
    }

    /**
     * Push task to staff for balancing 
     *
     * @return void
     */
    private function pushTaskToStaff($levelNumber, $staffId, $data)
    {
        if (count($this->EXCESS_TASK) > 0) {
            do {
                $keyLast = array_key_last($this->EXCESS_TASK);
                $data[] = $this->EXCESS_TASK[$keyLast];
                unset($this->EXCESS_TASK[$keyLast]);
            } while ($this->MAX_TOLERANS >= $this->getStaffTaskTotalTime($data) && count($this->EXCESS_TASK) > 0);

            $this->STAFF_TASKS[$levelNumber][$staffId] = $data;
        }
    }

    /**
     * Return total task time as hour for staff
     *
     * @return int
     */
    private function getStaffTaskTotalTime($data)
    {
        $totalTime = 0;
        foreach ($data as $val) {
            $totalTime += intval($val['estimated_duration']);
        }
        return $totalTime;
    }
}
