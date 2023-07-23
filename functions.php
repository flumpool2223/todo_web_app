<?php
define("STATUS_OPENED", "0");
define("STATUS_CLOSED", "1");
define("TODO_LIST_CSV", "todo_list.csv");
define("TODO_LIST_CSV_LOCK", "todo_list.csv.lock");

define("TASK_MAX_LENGTH", 140);
define("MESSAGE_TASK_EMPTY", "タスクが未入力です。");
define("MESSAGE_TASK_MAX_LENGTH", "タスクが140文字を超えています。");
define("MESSAGE_ID_INVALID", "入力されたIDは不正です。");

if(0 === strpos(PHP_OS, 'WIN')) {
  setlocale(LC_CTYPE, 'C');
}

function read_todo_list($include_closed = true)
{
    $handle = fopen(TODO_LIST_CSV, "r");
    $todo_list = [];
    while ($todo = fgetcsv($handle)) {
        if (!$include_closed
              && $todo[3] === STATUS_CLOSED) {
            continue;
        }
        $todo_list[] = $todo;
    }
    fclose($handle);
    return $todo_list;
}

function get_new_todo_id()
{
    return count(read_todo_list()) + 1;
}

function add_todo_list($todo)
{
    $handle = fopen(TODO_LIST_CSV, "a");
    fputcsv($handle, $todo);
    fclose($handle);
}

function write_todo_list($todo_list)
{
    $handle = fopen(TODO_LIST_CSV, "w");
    foreach ($todo_list as $todo) {
        fputcsv($handle, $todo);
    }
    fclose($handle);
}

function redirect($page)
{
    header("Location: " . $page);
    exit();
}

function redirect_with_message($page, $message)
{
    if (empty($message)) {
        redirect($page);
    }
    $message = urlencode($message);
    header("Location: " . $page
                        . "?message={$message}");
    exit();
}

function get_message()
{
  $message =
      (string)filter_input(INPUT_GET, "message");
  if ($message === MESSAGE_TASK_EMPTY
      || $message === MESSAGE_TASK_MAX_LENGTH
      || $message === MESSAGE_ID_INVALID) {
    return $message;
  }
  return "";
}

function lock_file($operation = LOCK_EX)
{
    $handle = fopen(TODO_LIST_CSV_LOCK, "a");
    flock($handle, $operation);
    return $handle;
}

function unlock_file($handle)
{
  flock($handle, LOCK_UN);
  fclose($handle);
}
