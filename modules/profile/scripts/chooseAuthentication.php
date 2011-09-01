<?php
$module = App::Get()->loadModule();
unset($_SESSION['authenticate']);
$_SESSION['authenticate'] = $_POST['auth'];

App::Get()->Redirect($module->moduleRoot . "/login");
