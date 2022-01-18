<?php

checkFileExist('access.log');

//Проверка наличия файла в системе
function checkFileExist($filePath){
    if (file_exists($filePath)) {
        echo "The file $filePath exists";
    } else {
        echo "The file $filePath does not exist";
        $path = 'access.log';
    }
    parseFile($filePath);
}

//Разбор файла
function parseFile($filePath)
{
    $pattern = "/(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")/";

    $fileContent = file_get_contents($filePath);

    preg_match_all($pattern, $fileContent, $resultArray);

    $bodyJSON = (object) [
        'views' => count($resultArray[0]),
        'urls' => count(array_unique($resultArray[12])),
        'traffic' => array_sum($resultArray[11]),
        'crawlers' => array_count_values(searchCrawlers($resultArray[13])),
        'statusCodes' => array_count_values($resultArray[10]),
    ];

    var_dump(json_encode($bodyJSON));
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







