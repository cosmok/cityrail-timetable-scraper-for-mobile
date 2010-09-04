<?php
date_default_timezone_set('Australia/Sydney');
require dirname(__FILE__) . '/stations.php';

$from = isset($argv[1]) ? $argv[1] : $_GET['from'];
$to   = isset($argv[2]) ? $argv[2] : $_GET['to'];
if (!in_array($from, $stations) || !in_array($to, $stations)) {
    exit('Cannot understand');
}
$url = constructURL('http://www.131500.com.au/plan-your-trip', array('from' => $from, 'to' => $to));
$scrape = @file_get_contents($url);
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
        $scheduleText = trim($entry2->nodeValue);
        preg_match('/Take\s*the\s*(.*)Dep:\s*([0-9apm:\+]*)\s*(.*)\s*Platform\s*(\d+)Arr:\s*([0-9apm:\+]*)\s*(.*)\s*Platform\s*(\d+)$/', $scheduleText, $matches);
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
echo json_encode($schedules);
function constructURL($url, $params)
{
    //http://www.131500.com.au/plan-your-trip/?Vehicle=Train&WalkSpeed=NORMAL&Priority=504&IsAfter=A&Date=31%2F7%2F2010&MaxChanges=-1&FromLocType=s&ToLocType=s&ViaLocType=&NotViaLocType=&Wheelchair=&Time=11%3A35AM&FromLoc=Croydon+Station~~%3BCroydon+Station%3BCroydon+Station~~LOCATION&ToLoc=Strathfield+%282135221%29~~%3BStrathfield+%282135221%29%3BStrathfield+%282135221%29~~LOCATION&x=28&y=20
    $genericParams = array(
                        'pmode' => 1,
                        'x'     => 10,
                        'y' => 17,
                        'Vehicle' => 'Train',
                        'Priority' => 504,
                        'MaxChanges' => '-1',
                        'FromLocType' => 's',
                        'ToLocType' => 's',
                        'IsAfter' => 'A',
                        'WalkSpeed' => 'NORMAL',
                        'ViaLocType' => '',
                        'NotViaLocType' => '',
                        'WheelChair' => '',
            );
    //Croydon+Station~~%3BCroydon+Station%3BCroydon+Station~~LOCATION&
    $newParams['FromLoc'] = $params['from'] . ' Station~~;' . $params['from'] . ' Station;' . $params['from'] . ' Station~~LOCATION';
    $newParams['ToLoc']   = $params['to'] . ' Station~~;' . $params['to'] . ' Station;' . $params['to'] . ' Station~~LOCATION';
    $newParams['Date']    = date('j:n:o');
    $newParams['Time']    = date('g:iA');
    return ($url . '/?'. http_build_query($genericParams + $newParams));
}
