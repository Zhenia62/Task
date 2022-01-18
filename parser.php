<?php

$jsonParse = checkFileExist('test.log');
//var_dump($jsonParse);
//Проверка наличия файла в системе
function checkFileExist($filePath): bool|string
{
    if (file_exists($filePath)) {
        echo "The file $filePath exists";
    } else {
        echo "The file $filePath does not exist";
        $path = 'access.log';
    }
    return parseFile($filePath);
}

//Разбор файла
function parseFile($filePath): bool|string
{
    $pattern = "/(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")/";

    $fileContent = file_get_contents($filePath);

    preg_match_all($pattern, $fileContent, $resultArray);
    $sumTraffic = getTraffic($resultArray);

    $bodyJSON = (object) [
        'views' => count($resultArray[0]),
        'urls' => count(array_unique($resultArray[8])),
        'traffic' => $sumTraffic,
        'crawlers' => array_count_values(searchCrawlers($resultArray[13])),
        'statusCodes' => array_count_values($resultArray[10]),
    ];

    return json_encode($bodyJSON);
}

//Получеине трафика сети
function getTraffic($resultArray): int
{
    $sumTraffic = 0;
    for ($i=0; $i<count($resultArray[11]); $i++){
        if ($resultArray[10][$i] != "301") {
            $sumTraffic += $resultArray[11][$i];
        }
    }

    return $sumTraffic;
}

//Поиск используемых поисковиков
function searchCrawlers($crawlersArray): array
{
    $searchArray = array('Google', 'Bing', 'Baidu', 'Yandex');
    $resultSearchArray = [];
    foreach ($crawlersArray as $itemCrawlers)  {
        foreach ($searchArray as $itemSearchArray) {
            if (strpos($itemCrawlers, $itemSearchArray))
                $resultSearchArray[] = $itemSearchArray;
        }
    }
    return $resultSearchArray;
}







