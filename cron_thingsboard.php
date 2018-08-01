<?php
include ('config/connect.php');
//API URL
$url = 'http://35.202.49.101:8080/api/v1/Y66bRv6YMV7u9LCVP1Dl/telemetry';
//create a new cURL resource
$ch = curl_init($url);
$data = array();

$HOST="35.224.119.170";
$USER="orangescrum";
$PASS="orangescrum";
$DB="orangescrum";
 
//Connecting to Database 
$con = mysqli_connect($HOST,$USER,$PASS,$DB) or die('Unable to Connect'); 
$fetch = mysqli_query($con,"SELECT (SELECT COUNT(id) FROM easycases WHERE istype='1' AND legend = 1) AS new,
		(SELECT COUNT(id) FROM easycases WHERE istype='1' AND legend = 2) AS in_progress,
		(SELECT COUNT(id) FROM easycases WHERE istype='1' AND legend = 5) AS resolved,
		(SELECT COUNT(id) FROM easycases WHERE istype='1' AND legend = 3) AS closed
		FROM easycases 
		WHERE istype='1' 
		AND isactive='1' 
		AND type_id!='10' 
		AND project_id!=0  
		AND project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects AS Project WHERE ProjectUser.project_id=Project.id AND Project.isactive='1') 
		GROUP BY legend ORDER BY FIELD(legend,1,6,2,4,5,3) LIMIT 1"); 

while ($row = mysqli_fetch_array($fetch, MYSQL_ASSOC)) {
    $row_array['new'] = $row['new'];
    $row_array['in_progress'] = $row['in_progress'];
    $row_array['resolved'] = $row['resolved'];
    $row_array['closed'] = $row['closed'];
}
array_push($data,$row_array);

$payload = json_encode($data);
//attach encoded JSON string to the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
//return response instead of outputting
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//execute the POST request
$result = curl_exec($ch);
//close cURL resource
curl_close($ch);
//Output response
echo "<pre>$result</pre>";
echo $payload;
//get response
$data = json_decode(file_get_contents('php://input'), true);
//output response
echo '<pre>'.$data.'</pre>';