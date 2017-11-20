<?php

require("Rest/RestClient.php");

$client = new RestClient("http://localhost/rest_cw12/server.php");
var_dump($client->query("GET", "resource=word&limit=1"));