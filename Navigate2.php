<?php
header("Content-Type: text/plain; charset=utf-8");
?>
 
<?php
mb_internal_encoding("UTF-8");
 
error_reporting(-1);
/* http://dobrokun.jp22.net/ */
 
define('SUBWAY', 'sub');
define('FOOT', 'foot');
define('BUS', 'bus');
 
$transportName = array(
    SUBWAY => 'едешь на метро',
    FOOT => 'идешь пешком',
    BUS => 'едешь на автобусе'
);
 
$startPoint = 'pet'; // Петроградская
$endPoint   = 'kre'; // Новая Голландия
 
$pointNames = array(
    'pet' => 'ст. м. Петроградская',
    'chk' => 'ст. м. Чкаловская',
    'gor' => 'ст. м. Горьковская',
    'spo' => 'ст. м. Спортивная',
    'vas' => 'ст. м. Василеостровская',
    'kre' => 'Петропавловская крепость',
    'let' => 'Летний сад',
    'dvo' => 'Дворцовая площадь',
    'isa' => 'Исакиевский собор',
    'nov' => 'Новая Голландия',
    'ras' => 'Дом Раскольникова',
    'gos' => 'Гостиный Двор',
    'sen' => 'Сенная Площадь',
    'vla' => 'ст. м. Владимирская',
    'vit' => 'Витебский вокзал',
    'teh' => 'Технологический Институт'
);
 
$paths = array(
    'pet' => array(
        'chk' => canGet(10, BUS),
        'gor' => canGet(3, SUBWAY)
    ),
 
    'chk' => array(
        'pet' => canGet(10, BUS),
        'spo' => canGet(3, SUBWAY)
    ),
 
    'gor' => array(
        'pet' => canGet(3, BUS),
        'kre' => canGet(5, FOOT),
        'gos' => canGet(6, SUBWAY)
    ),
 
    'spo' => array(
        'chk' => canGet(3, SUBWAY),
        'vas' => canGet(10, BUS),
        'sen' => canGet(7, SUBWAY)
    ),
 
    'vas' => array(
        'spo' => canGet(10, BUS),
        'gos' => canGet(7, SUBWAY),
        'nov' => canGet(11, FOOT)
    ),
 
    'kre' => array(
        'gor' => canGet(5, FOOT)
    ),
 
    'let' => array(
        'dvo' => canGet(6, FOOT),
        'gos' => canGet(7, FOOT)
    ),
 
    'dvo' => array(
        'isa' => canGet(6, FOOT),
        'gos' => canGet(6, FOOT),
        'let' => canGet(6, FOOT)
    ),
 
    'isa' => array(
        'dvo' => canGet(6, FOOT),
        'nov' => canGet(5, FOOT)
    ),
 
    'nov' => array(
        'vas' => canGet(11, FOOT),
        'isa' => canGet(5, FOOT),
        'ras' => canGet(7, BUS)
    ),
 
    'ras' => array(
        'nov' => canGet(7, BUS),
        'sen' => canGet(3, FOOT)
    ),
 
    'gos' => array(
        'vas' => canGet(7, SUBWAY),
        'sen' => canGet(3, SUBWAY),
        'dvo' => canGet(6, FOOT),
        'gor' => canGet(6, SUBWAY),
        'let' => canGet(7, FOOT),
        'vla' => canGet(7, FOOT)
    ),
 
    'sen' => array(
        'ras' => canGet(3, FOOT),
        'spo' => canGet(7, SUBWAY),
        'gos' => canGet(3, SUBWAY),
        'vla' => canGet(4, SUBWAY),
        'vit' => canGet(2, SUBWAY),
        'teh' => canGet(3, SUBWAY)
    ),
 
    'vla' => array(
        'sen' => canGet(4, SUBWAY),
        'gos' => canGet(7, FOOT),
        'vit' => canGet(3, SUBWAY)
    ),
 
    'vit' => array(
        'sen' => canGet(2, SUBWAY),
        'teh' => canGet(2, SUBWAY),
        'vla' => canGet(3, SUBWAY)
    ),
 
    'teh' => array(
        'sen' => canGet(3, SUBWAY),
        'vit' => canGet(2, SUBWAY)
    )
);
 
/* Чтобы не писать много раз array('time' => ..., 'by' => ...), используем функцию. 
«canGet» переводится как «можно попасть» */
 
function canGet($time, $byWhat)
{
    return array(
        'time' => $time,
        'by' => $byWhat
    );
}
$pathDone     = array();
$time         = 0;
$lowerTime = 5000;
$crossingTime = array();
function makeOneStep($paths, $startPoint, $endPoint, $time, $lowerTime, $pathDone)
{
 
    $pathDone[] = $startPoint;
    
    $result     = array();
    if (isset($paths[$startPoint][$endPoint])) {
        $pathDone[] = $endPoint;
        $time += $paths[$startPoint][$endPoint]['time'];
        $result['path'] = $pathDone;
        $result['time'] = $time;
        return $result; 
    }
   
    $shortest = array();
 
    foreach ($paths[$startPoint] as $stationName => $stationInfo) {
        if (!in_array($stationName, $pathDone)) {        	
            $newPath = makeOneStep($paths, $stationName, $endPoint, $time+=$paths[$startPoint][$stationName]['time'], $lowerTime, $pathDone);            
            if (!isset($newPath['time'])) {
            
            CONTINUE;
            }
           
            if (($newPath['time'] < $lowerTime)) {
 
                $lowerTime = $newPath['time'];
                $shortest  = $newPath;
            }
 
 
        }
    }
    $result = $shortest;
 
    return $result;
 
 
}
$path = makeOneStep($paths, $startPoint, $endPoint, $time, $lowerTime, $pathDone);
 
$text = "Маршрут построен: \n";
$i    = 0;
 
foreach ($path['path'] as $station) {
    if ($i == 0) {
        $text .= "Точка отправления:  $pointNames[$station] \n";
        $i++;
        CONTINUE;
    }
    if ($i == count($path['path']) - 1) {
        $text .= "Конечная точка: $pointNames[$station]. \n Затраченное время: {$path['time']}";
 
    } else {
 
        $text .= "$i $pointNames[$station] \n";
        $i++;
    }
}
echo $text;