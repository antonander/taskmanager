<?php

/**
 * Simple task manager with local database.
 *
 *
 * PHP version 7.3.18
 *
 *
 * @author     Anton Silva
 * @version    1.2.0
 * @since      File available since Release 1.2.0
 */

/*
* Database connection.
* Note: In order to use this file, you have to first make sure the
* taskmanager database exist and you have MySQL running. If you need further
* information, please refer to the README file.
*/
$host = "localhost";
$user = "root";
$pass = "root";
$db = "taskmanager";
try{
    $conn = mysqli_connect($host, $user, $pass, $db);
}
catch(Exception $e){
    exitMessage("The database is not connected. If you have questions, check the documentation.");
    die();
}

/**
 *
 *
 *
 *
 */

//Get the parameters from the console.
class Input {
   function getAction() {
       global $argv;
       return (isset($argv[1]) ? $argv[1] : null);
   }
   function getTaskName() {
       global $argv;
       if(isset($argv[2])){
         $task_name = null;
         for ($i=2; $i < sizeof($argv) ; $i++) {
           $task_name = $task_name.$argv[$i].' ';
         }
         $task_name = rtrim($task_name, "  ");
         return $task_name;
       }
       return (isset($argv[2]) ? $argv[2] : null);
   }
}
$input = new Input();

  //Manager class for tasks
  class TaskManager {

    private $name;
    private $start_datetime;
    private $end_datetime;
    private $last_start_datetime;
    private $time_elapsed;
    private $status;
    private $conn;

    public function __construct($conn) {

        $this->start_datetime = date("Y-m-d H:i:s");
        $this->end_datetime = date("Y-m-d 00:00:00");
        $this->last_start_datetime = date("Y-m-d H:i:s");
        $this->time_elapsed = "00:00:00";
        $this->status = "inactive";
        $this->conn = $conn;

    }

    //If it doesn't exist, it creates an entry on the database
    //with the task and its details.
    function start($task_name){

      $now = date("Y-m-d H:i:s");
        $sql = "INSERT IGNORE INTO tasks (name, start_datetime, end_datetime,
                last_start_datetime, status)
                VALUES ('".$task_name."', '".$now."', '".$this->end_datetime."',
                '".$now."', 'active')";

      return (mysqli_query($this->conn, $sql) ? true : false);
    }

    //Updates task with elapsed time and deactivates it.
    function stop($task_name){

        $time_elapsed  = $this->calculateElapsedTime($task_name);
        $end_datetime = date("Y-m-d H:i:s");

          $sql = "UPDATE tasks
                  SET time_elapsed = ADDTIME(time_elapsed,'".$time_elapsed."'),
                      end_datetime = '".$end_datetime."',
                      status = 'inactive'
                   WHERE name = '".$task_name."' AND status = 'active'";
          (mysqli_query($this->conn, $sql)
                  ? null
                  : exitMessage("Something went wrong. [100]"));

        return (mysqli_affected_rows($this->conn) != 0 ? true : false);
    }

    //Calculates de elapsed time based on start and end dates/time.
    function calculateElapsedTime($task_name){

        $sql = "SELECT last_start_datetime
                FROM tasks
                WHERE name = '".$task_name."'";

      $result = mysqli_query($this->conn, $sql);
      $row = mysqli_fetch_row($result);
      $last_start_datetime = $row[0];

      $startTime = new DateTime($last_start_datetime);
      $endTime = new DateTime(date("Y-m-d H:i:s"));
      $time_elapsed = date_diff($startTime,$endTime);
      $time_elapsed = $time_elapsed->format('%H:%I:%S');

      return $time_elapsed;

    }

    //Checks if there is a task running already.
    function isThereATaskRunning(){
        $sql = "SELECT name
                FROM tasks
                WHERE status = 'active'";
      $result = (mysqli_query($this->conn, $sql)
                 ? mysqli_query($this->conn, $sql)
                 : exitMessage("Something went wrong. [100]"));
      $name_task_running = mysqli_fetch_row($result);

      return ($name_task_running[0] != '' ? $name_task_running[0] : null);
    }

    //Updates task to refresh latest start date
    function updateTask($task_name){

        $now = date("Y-m-d H:i:s");

        $sql = "UPDATE tasks
                SET status = 'active', last_start_datetime = '".$now."'
                WHERE name = '".$task_name."'";

      return (mysqli_query($this->conn, $sql) ? true : false);
    }

    //Gets all tasks from database.
    function getAllTasks(){

         $sql = "SELECT DATE_FORMAT(start_datetime,'%H:%i:%s') AS start_time,
                        DATE_FORMAT(end_datetime,'%H:%i:%s') AS end_time,
                        time_elapsed,
                        status,
                        name
                 FROM tasks";

       $result = mysqli_query($this->conn, $sql);
       while ($row = mysqli_fetch_assoc($result)) {
         $tasks[] = $row;
       }
       return (mysqli_affected_rows($this->conn) != 0 ? $tasks : null);

     }

     //Outputs al tasks to the command line.
     function outputTasks($tasks){

       $mask = "\n|%-15s |%-15s |%-17s |%-17s |%-17s \n";
       printf($mask, 'Task name', 'Time elapsed',
             'First start time', 'Last stop time', 'Status');

       foreach (  $tasks  as $key => $task) {
         printf($mask, $task['name'], $task['time_elapsed'],
                $task['start_time'], $task['end_time'], $task['status']);
       }

       die;
     }

  }

  $task = new TaskManager($conn);

  //Show task summary by retrieving all tasks, and outputting the results.
  if(strcasecmp($input->getAction(), "summary") == 0){

    if($task->getAllTasks() != null){

      $task->outputTasks($task->getAllTasks());

    }else{

      exitMessage("You haven't added any tasks.");

    }

  }

  //Verify a task name is passed as an argument
  if($input->getTaskName() != null){

    if($input->getAction() == 'stop'){

      if($task->stop($input->getTaskName())){

         exitMessage("The task <".$input->getTaskName()."> was stopped.");

      }else{

         exitMessage("The task <".$input->getTaskName()."> isn't running.");
      }

    }

    if($input->getAction() == 'start'){

      if($task->isThereATaskRunning() != null){

        exitMessage("The task <".$task->isThereATaskRunning()."> is already running. Stop it before starting a new one.");

      }

      if(!$task->start($input->getTaskName())){

        exitMessage("The task <".$input->getTaskName()."> couldn't be started.");

      }else{

        if(!$task->updateTask($input->getTaskName())){

          exitMessage("The task <".$input->getTaskName()."> couldn't be updated.");

        }

        exitMessage("Current task: ".$input->getTaskName());
      }

    }

  }

 //Helper function
 function exitMessage($message){
   echo "\n".$message."\n";
   die;
 }

?>
