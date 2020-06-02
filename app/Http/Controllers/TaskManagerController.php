<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TaskManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         return view('taskmanager');
     }

     /**
      * Display a sum of the time elapsed for all tasks.
      *
      * @return string
      */

      public function getTotalTimeElapsed(){

         $row = DB::table('tasks')
                ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(time_elapsed)))'))
                ->first();

         foreach ($row as $key => $result) {
           $total_time[$key] = $result;
         }

         $total_time = array_values($total_time);
         return $total_time[0];
       }


     /**
      * Creates a new tasks if one with the same name doesn't exist already.
      *
      * @param  string  $name
      * @return bool
      */

      public function createNewTask($name){

        $now = date("Y-m-d H:i:s");
        $default_endtime = date("Y-m-d 00:00:00");

         $result = DB::table('tasks')->insertOrIgnore([
             [
              'name' => $name, 'time_elapsed' => '00:00:00',
              'start_datetime' => $now,
              'end_datetime' => $default_endtime,
              'last_start_datetime' => $now,
              'status' => 'active',
             ]
         ]);

         return $result;

      }

      /**
       * Retrieves last task added.
       *
       * @return array
       */

      public function getLastAddedTask(){

        $query = DB::table('tasks')->latest('start_datetime')->first();

        $result = json_decode(json_encode($query), true);

        return $result;

      }

      /**
       * Updates task by name.
       * Adds the new time elpased to the current one, itchanges
       * status to inactive and updates the new end_datetime.
       *
       * @param  string  $task_name, $time_elapsed
       */

      public function updateTask($task_name, $time_elapsed){

        $now = date("Y-m-d H:i:s");

        DB::statement("UPDATE tasks SET time_elapsed =
          ADDTIME(time_elapsed, '".$time_elapsed."'), end_datetime = '".$now."',
          status = 'inactive' WHERE name = '".$task_name."'");

      }

      /**
       * Restarts existing task.
       * @param  string  $task_name
       * @return int
       */

      public function restartTask($task_name){

        $now = date("Y-m-d H:i:s");

        $affected = DB::table('tasks')
                      ->where('name', $task_name)
                      ->update(['status' => 'active',
                                'last_start_datetime' => $now,
                               ]);
        return $affected;

      }

      /**
       * Remove the specified resource from storage.
       *
       */

      public function destroyAll()
      {
          DB::table('tasks')->delete();
      }
}
