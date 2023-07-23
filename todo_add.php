<?php
require_once("functions.php");

$task = (string)filter_input(INPUT_POST, "task");
if ($task === "") {
  redirect_with_message("todo_list.php", MESSAGE_TASK_EMPTY);
}

if (mb_strlen($task) > 140) {
  redirect_with_message("todo_list.php", MESSAGE_TASK_MAX_LENGTH);
}

$lock_handle = lock_file();

$id = get_new_todo_id();
$date = date('Y-m-d H:i:s');//リアルタイムがなぜか取れていないので修正必要
$status = STATUS_OPENED;
$todo = [$id, $task, $date, $status];

add_todo_list($todo);

unlock_file($lock_handle);

redirect("todo_list.php");
?>