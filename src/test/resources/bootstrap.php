<?php

define("APPLICATION_PATH", __DIR__ . "/../../..");
$loader = require(APPLICATION_PATH . "/vendor/autoload.php");
$loader->addPsr4("guymers\\proxy\\mock\\", __DIR__ . "/../php/guymers/proxy/mock");
