<table class="table" id="summary">
  <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col" class="text-right">Time spent on task</th>
    </tr>
  </thead>
  <tbody>
    <tr>
    </tr>
    @foreach ($tasks as $task)
    <tr>
      <td>{{ $task->name }}</td>
      <td class="text-right">{{ $task->time_elapsed }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<p class="text-center">Total time invested today: <br> <span class="total-time"><?php echo $task_manager_controller->getTotalTimeElapsed() ?></span> </p>
