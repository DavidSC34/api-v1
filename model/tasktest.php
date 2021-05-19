<?php
require_once('Task.php');

try {

    $task = new Task(1, "Title Here", "Description Here", "01/01/2019 12:00", 'N');
    header('Content-Type: application/json;chtset=UTF-8');

    echo json_encode(($task->returnTaskAsArray()));
} catch (TaskException $ex) {

    echo "Error: " . $ex->getMessage();
}
