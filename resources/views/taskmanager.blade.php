<?php

/**
 * Simple task manager with local database.
 *
 *
 * PHP version 7.3.18.
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @author     Anton Silva
 * @version    1.2.0
 * @since      File available since Release 1.2.0
 */

    use App\Http\Controllers\TaskManagerController;

    //Create an instance to access the controller
    $task_manager_controller = new TaskManagerController();

    //Get all tasks
    $tasks = App\TaskManager::all();

    //Convert object to array
    $tasks_array = json_decode(json_encode($tasks), true);

    //If a request is received
    if(isset($_POST["task_name"])){

      //Take request parameters
      $task_name = $_POST["task_name"];
      $time_elapsed = $_POST["time_elapsed"];


      if($_POST["action"] == "stop"){

        //Records the new elapsed time.
        $task_manager_controller->updateTask($task_name, $time_elapsed);

        die;
      }


      if($_POST["action"] == "delete"){

        //Delete all records from table.
        $task_manager_controller->destroyAll();

        die;
      }


      if($_POST["action"] == "resume"){

        //If it doens't exist, create a new task
        $task_manager_controller->createNewTask($task_name);

        //Retrieve last task created
        $last_task_added = $task_manager_controller->getLastAddedTask();

        //If the task already existed
        if($last_task_added["name"] == $task_name){

          //Restart it
          $task_manager_controller->restartTask($task_name);

        }

        die;

      }

    }

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <title>Task Manager</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
  </head>
  <body>
    <div class="container p-3 mt-3">
        <div class="h-100 row align-items-center">
          <div class="col-12">
            <h1 class="text-center">Task manager</h1>

            <p class="text-center" id="chronometer">00:00:00</p>
            <p class="text-center my-4" id="current_task">You will see your current task here</p>

            <form id="task-form" onsubmit="return false" action="">
              <input type="text" class="d-block mx-auto c-form-control" name="task" id="task_name" value="" placeholder="Your task" require>
              <div class="text-center p-3">
                <button class="input_button btn btn-success" type="submit" id="resume_task_btn">Start</button>
                <button class="input_button btn" type="submit" id="stop_task_btn" disabled>Stop</button>
              </div>
            </form>
          </div>
          </div>

          <div class="row justify-content-center mt-4" id="summary-section">
            <div class="col-12 col-lg-6">
              <p>A summary of the tasks you have covered so far</p>

              @if (empty($tasks_array))
                <div class="no-tasks-notice p-3 mb-3 mb-lg-4">
                  You have no tasks yet.
                </div>
                <p class="text-center">Total time invested today: <br> <span class="total-time">00:00:00</span> </p>
              @else
                @include('summary')
              @endif

            </div>
          </div>
          <div class="text-center">
            <button class="btn btn-secondary delete" id="delete_tasks">Start from scratch</button>
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('js/main.min.js') }}"></script>
    <script type="text/javascript">

    /*
    * Short description.
    * A timer is created and, depending on what action the user takes,
    * a set of elements change their state and a request is sent to the
    * handler.
    *
    */

    $(document).ready(function () {

          var timer = new Timer();
          var action = "";

          // Link timer to display for user.
          timer.addEventListener('secondsUpdated', function(e) {
            $('#chronometer').html(timer.getTimeValues().toString());
          });

          //Sends data to controller-handler.
          function updateDatabase(task_name, time_elapsed, action){
            $.ajax({
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              method:"POST",
              url: "/",
              data: {task_name : task_name, time_elapsed : time_elapsed, action : action},
              success:function(data){
                $("#summary-section").load(location.href+" #summary-section>*","");
              }
            });
          }

          $("#delete_tasks").on('click', function(){

            if(action != "resume"){
              var conf = confirm("Press 'Ok' to delete your existing tasks.");
             if (conf == true) {
               var task_name = " ";
               var time_elapsed = " ";
               action = "delete";
               updateDatabase(task_name, time_elapsed, action);
             }
            }
          });

          // Decides on action based on input.
          $("body").delegate(".input_button", "click", function(){

            var task_name = $('#task_name').val();
            var time_elapsed = timer.getTimeValues().toString();

            //If a task name is given
            if(task_name != ''){

              //Change elements' status
              $('.input_button').prop('disabled', false);
              $(this).prop('disabled', true);
              $(this).removeClass('btn-success');

                  //If the button pressed was start...
                  if($(this).attr('id') == "resume_task_btn"){

                    //Timer starts
                    timer.start();
                    action = "resume";

                    //Change elements' status
                    $('.delete').removeAttr('id');
                    $('#task_name').prop('disabled', true);
                    $("#stop_task_btn").addClass('btn-success');

                    //Display the current task for the user.
                    $('#current_task').html('Current task: ' + task_name);

                  //If the button pressed was stop...
                  }else{
                    timer.stop();
                    action = "stop";

                    //Change elements' status
                    $('.delete').attr('id', 'delete_tasks');
                    $('#task_name').prop('disabled', false);
                    $("#resume_task_btn").addClass('btn-success');
                  }

                  //Sends data to controller-handler.
                  updateDatabase(task_name, time_elapsed, action);
                }

              });

    });
    </script>

  </body>
</html>
