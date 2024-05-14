<?php



if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $dateObject = DateTime::createFromFormat('d.m.Y', $date);
    if ($dateObject === false) {
        echo 'Введите дату в формате 01.01.2001';
        exit;
    }
    $date = $dateObject->format('d/m/Y');
} else {
    $date = date('d/m/Y');
}


print_r($date);

$url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $date;
$contents = file_get_contents($url);
//$contents = iconv('windows-1251', 'utf8', $contents);

$xml = simplexml_load_string($contents);

$currencies = [];

foreach($xml->Valute as $value) {
    $name = (string)$value->Name;
    $code = (string)$value->CharCode;
    $rate = (string)$value->VunitRate;
    $rate = str_replace(',', '.', $rate);
    $rate = (float)$rate;
    $currencies[] = [
        'name' => $name,
        'code' => $code,
        'rate' => $rate,
    ];
}

$delimiter = ';';
$csv = fopen('currencies.csv', 'w');

fprintf($csv, chr(0xEF) . chr(0xBB) . chr(0xBF));


fputcsv($csv, [
    'Назвние валюты',
    'Код валюты',
    'Курс валюты'
 ], $delimiter);

foreach ($currencies as $currency) {
    $rate = (string)$currency['rate'];
    $rate = str_replace('.', ',', $rate);
    fputcsv($csv, [
        $currency['name'],
        $currency['code'],
        $rate,
    ], $delimiter);
}

fclose($csv);



//var_dump($currencies);

