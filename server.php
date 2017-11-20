<?php 

require("Service.php");
require("Rest/RestServer.php");

$server = new RestServer("Service");

$server->handle();