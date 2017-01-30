<?php 
$lastphpupdate = "last update 2017-01-30 13:19 \n"; // info last update php 

// this php will update emtpy country and continent code. to run:
// $> php kci_update_country.php

/*
database: kci
table: kci_logipv4
logdate datetime
logipv4 int(11)
logmsg varchar(1000)
kci_user int(11)
kci_category int(11)
id int(11)
codecontinent char(2)
codecountry2 char(2)
codecountry3 char(3)

table: kci_user
id int(11)
userdisplay varchar(100)
uservalidatefile varchar(100)
uservalidatestatus tinyint(4)
kci_login int 
userip int

table: kci_login
id int(11)
username varchar(100)
pass varchar(255) 

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
actionban = curl --data 'category=<category>' --data 'logipv4=<ip>' --data-urlencode 'logmsg=<matches>' --user-agent 'fail2ban v0.8.12' 'http://www.garasiku.web.id/web/kci/kci_log.php' >> /home/garasiku/test/curlfail2ban.log
...

*/

// change user and possword with yours
// parameters: host, user, password, database, [port]
$mysqli = new mysqli("localhost", "user", "password", "myf2b");
if ($mysqli->connect_errno) {
  die('database error -end-');
}

$idipv4[] = array();
$logipv4;
$logmsg;
$codecontinent="";
$codecountry2="";
$codecountry3="";

$isprocess = true;
// Todo adding kci_user & kci_category
$kci_user = 1;
$kci_category=10;

$error_msg = "\n";


// if request is valid
if ($isprocess) {
	// getting user id base on user ip and uservalidatefile (NOT YET)
	$error_msg = "No more null or empty in continent and country code\n";
	$isprocess = false;

	// read all date with empty continent or country
	//$sql = "SELECT id, logipv4 FROM kci_logipv4 WHERE kci_user=".$kci_user." and (codecontinent is null or codecontinent ='' or codecountry2 is null or codecountry2 ='' or codecountry3 is null or codecountry3='')";
	$sql = "SELECT id, logipv4 FROM kci_logipv4 WHERE (codecontinent is null or codecontinent ='' or codecountry2 is null or codecountry2 ='' or codecountry3 is null or codecountry3='')";
	$res = $mysqli->query($sql);
	$rows = $res->num_rows; 
	//echo $sql."\n"; // debug
	echo "Number of rows: ".$rows."\n";

	if ($rows>0) {
		while ($row = $res->fetch_assoc()) {
			$tmpid = intval($row['id']);
			$tmplogipv4 = long2ip($row['logipv4']);
			$isprocess = true;
			$idipv4[$tmpid]= $tmplogipv4;
			//echo $tmpid." ".$tmplogipv4."\n"; // debug
		}
	}
	
	//echo count($idipv4)."\n"; // debug
	// update continent or country
	foreach ($idipv4 as $id => $ipv4) {
		//echo $id." ".$ipv4."\n"; // debug
		$codecontinent=geoip_continent_code_by_name($ipv4);
		$codecountry2=geoip_country_code_by_name($ipv4);
		$codecountry3=geoip_country_code3_by_name($ipv4);
		$sql = "UPDATE kci_logipv4 set codecontinent='$codecontinent', codecountry2='$codecountry2', codecountry3='$codecountry3' WHERE id=$id";
		//echo $sql."\n"; // debug
		$res = $mysqli->query($sql);
		if (!$res)
			echo "Fail: ".$mysqli->errno." ".$mysqli->error."\n";
	}

} 

if (!$isprocess) {
	echo $error_msg;
}
?> 
