<?php
require_once("functions.php");

$id = (int)filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id <= 0) {
  redirect_with_message("todo_list.php", MESSAGE_ID_INVALID);
}

$lock_handle =lock_file();

$todo_list = read_todo_list();
foreach ($todo_list as &$todo) {
  if ((int)$todo[0] === $id) {
    $todo[3] = STATUS_CLOSED;
    break;
  }
}
write_todo_list($todo_list);

unlock_file($lock_handle);

redirect("todo_list.php");