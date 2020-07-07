<?php

require_once __DIR__ . '/ConfigParser.php';

var_dump((new ConfigParser(__DIR__ . '/config.txt'))->parse());