<?php 
// only localhost can access
if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']){
  die('Internal Server Error maybe service -end-');
}
// Open Source
// Write by: IGAM Muliarsa 2017-01-18
// For Education Purpose 
// Use it with your own risk
// No Support
/*
database: myf2b
table: kci_logipv4
logdate datetime
logipv4 int(11)
logmsg varchar(1000)
kci_category int(11) 
id int(11)
codecontinent char(2)
codecountry2 char(2)
codecountry3 char(3)
table: kci_category
id int(11)
category varchar(20)
| 10 | SSH                  |
| 20 | FTP                  |
| 30 | HTTP/HTTPS           |
| 40 | SMTP/POP/IMAP/POP3/S | 
///etc/fail2ban/jail.conf
...
action = iptables-ipset-proto4[]
  mlocaldb[category=40]
...
///etc/fail2ban/action.d/mlocaldb.conf
...
actionban = curl --data 'category=<category>' --data 'logipv4=<ip>' --data-urlencode 'logmsg=<matches>' --user-agent 'fail2ban v0.8.12' 'http://[your_domain]/kci_log.php' >> /home/[user]/logs/curlfail2ban.log
...
*/
$curdatetime = new DateTime("now");
echo $lastphpupdate; // print last update php 
echo $curdatetime->format('Y-m-d\TH:i:s')." : Starting curl\n";
// change user and possword with yours
// parameters: host, user, password, database, [port]
$mysqli = new mysqli("localhost", "user", "password", "myf2b");
if ($mysqli->connect_errno) {
  die('database error -end-');
}
$logipv4;
$logmsg;
$codecontinent="";
$codecountry2="";
$codecountry3="";
$isprocess = false;
$kci_category=10; // initialize category
$userip = ip2long($_SERVER['REMOTE_ADDR']); // we store it in long, that's why we convert it
$error_msg = "not Process: Parameters input not valid -end- \n"; 
// POST
if (isset($_POST["logipv4"]) && isset($_POST["logmsg"]) && isset($_POST["category"]))
{
	$isprocess = true;
	$hostip=$_POST["logipv4"];
	$codecontinent=geoip_continent_code_by_name($hostip);
	$codecountry2=geoip_country_code_by_name($hostip);
	$codecountry3=geoip_country_code3_by_name($hostip);
	$logipv4 = ip2long($hostip); //sanitize	$logmsg = $_POST["logmsg"]; // sanitize latter
	$kci_category = intval($_POST["category"]); // sanitize
}
// GET
if (isset($_GET["logipv4"]) && isset($_GET["logmsg"]) && isset($_GET["category"]))
{
	$isprocess = true;
	$hostip=$_GET["logipv4"];
	$codecontinent=geoip_continent_code_by_name($hostip);
	$codecountry2=geoip_country_code_by_name($hostip);
	$codecountry3=geoip_country_code3_by_name($hostip);
	$logipv4 = ip2long($hostip); //sanitize
	$logmsg = $_GET["logmsg"]; // sanitize latter
	$kci_category = intval($_GET["category"]); // sanitize
}
 
// if request is valid
if ($isprocess) {
	// some sequence has been deleted
	
	// check user cannot send 2 report with the same category and IP attacker at least 24 hour
	if ($isprocess) {
		$sql = "SELECT * FROM kci_logipv4 WHERE kci_category=".$kci_category." AND logipv4=".$logipv4." ORDER BY logdate";
		$res = $mysqli->query($sql);
		//echo $sql."\n";
		$rows = $res->num_rows; 
		if ($rows>0) {
			while ($row = $res->fetch_assoc()) {
				$lastdatetime = new DateTime($row['logdate']); // create date time object
				//$lastdatetime->add(new DateInterval('PT1H')); // last insert with paricular IP we add 1 hour
				$lastdatetime->add(new DateInterval('P1D')); // last insert with paricular IP we add 1 day
				if ($curdatetime<=$lastdatetime) {
					$isprocess = false;
					$error_msg = "not Process: can not add/report the same IP less than 1 hour -end- \n";
					break;
				}
			}
		}
	}
	// final process: try to inserting data if everyting is OK
	if ($isprocess) {
		// Securing input 
		// sanitize logmsg 
		// replace new line with <br>
		$logmsg=nl2br($logmsg);
		$logmsg = $mysqli->escape_string($logmsg); // sanitize
		echo "Insert \n";
		echo "matches: $logmsg \n";
		$sql = "INSERT INTO kci_logipv4(logdate, logipv4, logmsg, kci_category, codecontinent, codecountry2, codecountry3) VALUES ('".$curdatetime->format('Y-m-d H:i:s')."', ".$logipv4.", '".$logmsg."', ".$kci_category.", '".$codecontinent."', '".$codecountry2."', '".$codecountry3."' );";
		if (!$mysqli->query($sql)) {
			$isprocess = false;
			$error_msg = "insert error: fail insert -end- \n";
		} else {
			echo "done -end- \n";
		}
	}
} 
if (!$isprocess) {
	echo $error_msg;
}
?> 
