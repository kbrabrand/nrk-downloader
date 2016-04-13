<?php

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Cookie: NRK_PLAYER_SETTINGS_TV=devicetype=desktop&preferred-player-odm=hlslink&preferred-player-live=hlslink&max-data-rate=3500;"
  )
);

$context = stream_context_create($opts);

$html = file_get_contents($argv[1], false, $context);

preg_match('/data-video-id\s?=\s?"(.*)"/', $html, $programIdMatch);
preg_match('/data-subtitlesurl\s?=\s?"(.*)"/', $html, $subtitleUrlMatch);
preg_match('/href="(.*\.m3u8)"/', $html, $hlsMediaMatch);
preg_match('/springStreamStream":"([\-\.a-zA-ZæåøÆÅØ]+\/)*([^"]+)/', $html, $keyMatch);

if (!$programIdMatch) {
    echo 'Program id not found';
}

if (!$keyMatch) {
    echo 'Program key not found';
    exit;
}

if (!$hlsMediaMatch) {
    echo 'No HLS link found on page';
    exit;
}

$programId   = $programIdMatch[1];
$key         = str_replace('.' . $programId, '', $keyMatch[2]);
$subtitleUrl = null;

if ($subtitleUrlMatch) {
    $subtitleUrl = sprintf('http://tv.nrk.no%s', $subtitleUrlMatch[1]);
}

$m3u8 = file_get_contents($hlsMediaMatch[1]);
$m3u8Lines = explode("\n", $m3u8);

$maxBandwidth = 0;
$streamLink   = '';

foreach ($m3u8Lines as $index => $line) {
    if (substr($line, 0, 1) !== '#') {
        continue;
    }

    preg_match('/BANDWIDTH=([0-9]+),/', $line, $bandwidthMatch);

    if ($bandwidthMatch && $bandwidthMatch[1] > $maxBandwidth) {
        $maxBandwidth = $bandwidthMatch[1];
        $streamLink = $m3u8Lines[$index + 1];
    }
}

echo sprintf('%s %s %s', $key, $streamLink, $subtitleUrl);
