<?php

namespace Kutluturkan\ToDoPlanning\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Kutluturkan\ToDoPlanning\Models\ToDoList;

class ToDoListRegister extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todolist:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Do List install to table from json file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $ToDoListDataConfig = config('todoplanning.todolist');

            foreach ($ToDoListDataConfig as $conf_val) {

                $data = $this->getFile($conf_val['url']); // Get Json Data from URL 

                if (is_array($data)) {
                    $this->registerData($data, $conf_val);
                }
            }

            $this->info(PHP_EOL . "All task list has been registered");
        } catch (\Exception $e) {
            $this->error('Could not load data');
        }
    }

    /**
     * Register JSON data to DB
     *
     * @return void
     */
    private function registerData($data, $conf)
    {
        try {
            $sleep_count = intval(config('todoplanning.sleep_count'));

            $bar = $this->output->createProgressBar(count($data));
            $bar->start();

            DB::beginTransaction();

            $counter = 1;
            foreach ($data as $key => $val) {

                if ($counter++ % $sleep_count == 0) sleep(2);

                if (is_array($val) && $conf['name_is_array_key'] === true) {

                    //First patter register settings

                    $name = trim(array_key_first($val));
                    $level = intval(trim($val[$name][$conf['level']]));
                    $estimatedDuration = intval(trim($val[$name][$conf['estimated_duration']]));

                    if (!empty($name) && $level > 0 && $estimatedDuration > 0) {
                        ToDoList::firstOrCreate(
                            ['name' => $name],
                            ['level' => $level, 'estimated_duration' => $estimatedDuration]
                        );
                    } else {
                        $this->warn(PHP_EOL . "Could Not Load ! " . $name . "|" . $level . "|" . $estimatedDuration);
                        continue;
                    }
                } else {

                    //Second pattern register register

                    $name = trim($val[$conf['name']]);
                    $level = intval(trim($val[$conf['level']]));
                    $estimatedDuration = intval(trim($val[$conf['estimated_duration']]));

                    if (!empty($name) && $level > 0 && $estimatedDuration > 0) {
                        ToDoList::firstOrCreate(
                            ['name' => $name],
                            ['level' => $level, 'estimated_duration' => $estimatedDuration]
                        );
                    } else {
                        $this->warn(PHP_EOL . "Could Not Load ! " . $name . "|" . $level . "|" . $estimatedDuration);
                        continue;
                    }
                }
                $bar->advance();
            }
            $bar->finish();

            DB::commit();
            $this->info(' Complated ! ' . $conf['url']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Could not register data to database');
        }
    }


    /**
     * Get JSON file content as array collection
     *
     * @return array
     */
    private function getFile($fileUrl)
    {
        try {
            return json_decode(file_get_contents($fileUrl), true);
        } catch (\Exception $e) {
            $this->error('Url not found');
            return [];
        }
    }
}
