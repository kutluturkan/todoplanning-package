<?php

namespace Kutluturkan\ToDoPlanning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToDoList extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'to_do_lists';
    public $timestamps = true;
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'estimated_duration',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getToDoListShorting()
    {
        $responseData = [];
        $toDoData = $this->orderBy('level', 'desc')->orderBy('estimated_duration', 'desc')->get()->toArray();

        foreach ($toDoData as $key => $val) {
            $responseData[$val['level']][$key] = $val;
        }

        return $responseData;
    }

    public function maxDurationTime()
    {
        return $this->orderBy('estimated_duration', 'desc')->first();
    }
}
