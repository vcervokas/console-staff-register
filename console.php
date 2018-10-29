<?php

    use controllers\ConsoleController;

    require_once 'vendor/autoload.php';

    $console = new ConsoleController();
    $console->handleRequests($argc, $argv);

    exit;
