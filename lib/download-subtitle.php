<?php

date_default_timezone_set('Europe/Oslo');

// Too few arguments
if (count($argv) < 3) {
    exit;
}

$key         = $argv[1];
$subtitleUrl = $argv[2];
$dstFile     = getcwd() . DIRECTORY_SEPARATOR . $key . '.nb.srt';

// Skip if subtitle file exists already
if (is_file($dstFile)) {
    echo 1;
    exit;
}

if (!is_writable($dstFile)) {
  echo $dstFile . ' is not writeable' . PHP_EOL;
}

$output = fopen($dstFile, 'w');

$subtitleXML = file_get_contents($subtitleUrl);

$lines = explode("\n", $subtitleXML);

// Subtitle sequence number
$index = 0;

foreach ($lines as $line) {
    if (preg_match('/<p begin="(\d{2}:\d{2}:\d{2}).(\d{3})" dur="(\d{2}):(\d{2}):(\d{2}).(\d{3})"( style="([a-zA-Z]+)")?>(.*?)<\/p>/', $line, $match)) {
        list(, $begin, $beginDecimals, $dh, $dm, $ds, $dd, , $style, $text) = $match;

        $prefix = '';
        $suffix = '';

        if ($style === 'italic') {
            $prefix = '<p>';
            $suffix = '</p>';
        }

        $startTime = $begin . '.' . $beginDecimals;
        $endTime   = new DateTime($begin);

        // Add duration
        if ($dh > 0 || $dm > 0 || $ds > 0) {
            $endTime->add(new DateInterval(sprintf('PT%sH%sM%sS', (int) $dh, (int) $dm, (int) $ds)));
        }

        // Add decimal overflow
        if ($beginDecimals + $dd > 1000) {
            $endTime->add(new DateInterval('PT1S'));
        }

        // Format time and append decimals
        $endTime = $endTime->format('H:i:s') . '.' . str_pad(($beginDecimals + $dd) % 1000, 3, '0', STR_PAD_LEFT);

        $line = implode(PHP_EOL, [
            ++$index,
            sprintf('%s --> %s', $startTime, $endTime),
            str_replace('<br />', PHP_EOL, $text),
            PHP_EOL
        ]);

        fwrite($output, $line);
    }
}

fclose($output);

return 1;
