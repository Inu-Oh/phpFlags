<?php
header("Content-type: application/json; charset=utf-8");

$jsonData = file_get_contents('countries.json');
$countryList = json_decode($jsonData, true);

$randomCountry = $countryList[array_rand($countryList)];
$randomCountry['src'] = 'static/images/'.$randomCountry['code'].'.png';
echo(json_encode($randomCountry, JSON_PRETTY_PRINT));