<?php

date_default_timezone_set('Europe/Oslo');

// Too few arguments
if (count($argv) < 2) {
    exit;
}

$key = $argv[1];

if ($key && stripos($key, DIRECTORY_SEPARATOR) && !file_exists(dirname($key))) {
    if (!mkdir(dirname($key), 0777, true)) {
      exit(1);
    }
}
