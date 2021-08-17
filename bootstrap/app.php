<?php

require_once ("../config/app.php");
require_once ("../config/database.php");

/// reserved routes
require_once ("../routes/web.php");
require_once ("../routes/api.php");


/// run routing system
$routing = new System\Router\Routing();
$routing->run();


