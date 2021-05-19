<?php

require_once('db.php');
require_once('../model/Task.php');
require_once('../model/Response.php');

try {
    $writeDB = DB::connectWriteDB();
    $readDB = DB::connectReadDB();
} catch (PDOException $ex) {
    //throw $th;
    error_log("Connection error - " . $ex, 0); //solo el webmaster lo puede ver
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database coonection error"); //mensaje para el usuario final
    $response->send();
    exit();
}


if (array_key_exists("taskid", $_GET)) {

    $taskid = $_GET['taskid'];

    if ($taskid == '' || !is_numeric($taskid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Task Id cannot be blank or must be numeric");
        $response->send();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        try {
            //code...
            $query = $readDB->prepare(" SELECT id, title, description, DATE_FORMAT(deadline,'%d/%m/%Y %H:%i'), complete FROM tasks WHERE id= :taskid ");
            $query->bindParam(':taskid', $taskid, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Task not Found");
                $response->send();
                exit;
            }

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $task = new Task($row['id'], $row['title'], $row['description'], $row['deadline'], $row['complete']);
                $taskArray[] = $task->returnTaskAsArray();
            }

            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $returnData['tasks'] = $taskArray;

            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->toCache(true);
            $response->setData($returnData);
            $response->send();
            exit;
        } catch (TaskException $ex) {

            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
            //throw $th;
        } catch (PDOException $ex) {
            //throw $th;
            error_log("Database query error - " . $ex, 0); //solo el webmaster lo puede ver
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("Fail to get Task"); //mensaje para el usuario final
            $response->send();
            exit();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    } else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request Method Not allowed");
        $response->send();
        exit;
    }
}
