<?php
require_once('Response.php');

$response = new Response();

$response->setSuccess(false);
$response->setHttpStatusCode(404);
$response->addMessages("Error with value");

$response->send();