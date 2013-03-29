<?php
date_default_timezone_set('Australia/Sydney');
require dirname(__FILE__) . '/stations.php';

$from = isset($argv[1]) ? $argv[1] : $_GET['from'];
$to   = isset($argv[2]) ? $argv[2] : $_GET['to'];
if (!in_array($from, $stations) || !in_array($to, $stations)) {
    exit('Cannot understand');
}
$url = constructURL('http://www.131500.com.au/plan-your-trip/trip-planner', array('from' => $from, 'to' => $to));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$scrape = curl_exec($ch);
curl_close($ch);
if (!$scrape) {
    exit('cannot');
}
$scrape = str_replace('&nbsp;', ' ', $scrape);
$doc = new DOMDocument();
@$doc->loadHTML($scrape);
$xpath = new DOMXpath($doc);

/* Search for <a><b><c> */
$results = $xpath->query('//table[contains(@class,"widthcol2and3")]');
$schedules = array();
foreach ($results as $i => $entry) {
    $doc2 = new DomDocument();
    $doc2->appendChild($doc2->importNode($entry, true));
    $xpath2 = new DOMXPath($doc2);
    $results2 = $xpath2->query('//tbody/tr/td[2]');
    foreach($results2 as $j => $entry2) {
        $scheduleText = preg_replace("/\\n\\s*/", " ", trim($entry2->nodeValue));
        preg_match('/Take\s*the\s*(.*)Dep:\s*([0-9apm:\+]*)\s*(.*)\s*Platform\s*(\d+)\s*Arr:\s*([0-9apm:\+]*)\s*(.*)\s*Platform\s*(\d+)(.*)/', $scheduleText, $matches);
        if (!empty($matches)) {
            $schedules[$i][$j]['train'] = $matches[1];
            $schedules[$i][$j]['depTime'] = $matches[2];
            $schedules[$i][$j]['depStation'] = $matches[3];
            $schedules[$i][$j]['depPlatform'] = $matches[4];
            $schedules[$i][$j]['arrTime'] = $matches[5];
            $schedules[$i][$j]['arrStation'] = $matches[6];
            $schedules[$i][$j]['arrPlatform'] = $matches[7];
        }
    }
}
$output['cityrailURL'] = $url;
$output['schedules']   = $schedules;
echo json_encode($output);
function constructURL($url, $params)
{
    //http://www.131500.com.au/plan-your-trip/trip-planner?session=invalidate&itd_anyObjFilter_origin=2&itd_name_origin=Strathfield&itd_anyObjFilter_destination=2&itd_name_destination=Croydon&itd_itdDate=20100904&itd_itdTripDateTimeDepArr=dep&itd_itdTimeHour=6&itd_itdTimeMinute=45&itd_itdTimeAMPM=pm&itd_includedMeans=checkbox&itd_inclMOT_7=1&itd_inclMOT_1=Train&itd_trITMOT=100&itd_trITMOTvalue100=15&itd_changeSpeed=normal&itd_routeType=LEASTINTERCHANGE&x=76&y=16
    $genericParams = array(
                        //'session' => 'invalidate',
                        'itd_anyObjFilter_origin' => 2,
                        'itd_anyObjFilter_destination' => 2,
                        'itd_itdTripDateTimeDepArr' => 'dep',
                        'itd_includedMeans' => 'checkbox',
                        'itd_inclMOT_7' => 1,
                        'itd_inclMOT_1' => 'Train',
                        'itd_trITMOT' => 100,
                        'itd_trITMOTvalue100' => 15,
                        'itd_changeSpeed' => 'normal',
                        'itd_routeType' => 'LEASTINTERCHANGE',
                        'x'     => 76,
                        'y'     => 16,
            );
    $newParams['itd_name_origin'] = $params['from'];
    $newParams['itd_name_destination'] = $params['to'];
    $newParams['itd_itdDate']    = date('omd');
    $newParams['itd_itdTimeHour']    = date('g');
    $newParams['itd_itdTimeMinute']    = date('i');
    $newParams['itd_itdTimeAMPM']    = date('a');
    $query = $url . '?'. http_build_query($genericParams + $newParams);
    return ($query);
}
