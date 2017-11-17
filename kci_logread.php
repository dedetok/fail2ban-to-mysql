<?php 
session_start();
define("kci",1);

$lastphpupdate = "last update 2017-02-04 09:40 \n"; // info last update php  

// Open Source
// Write by: IGAM Muliarsa 2016-10-17
// For Education Purpose 
// Use it with your own risk
// No Support

// flags from http://www.famfamfam.com/lab/icons/flags/

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

*/

/* continent code
AF	Africa
AN	Antarctica
AS	Asia
EU	Europe
NA	North america
OC	Oceania
SA	South america
http://php.net/manual/en/function.geoip-continent-code-by-name.php
*/

$continent = array(
	"AF" => "Africa",
	"AN" => "Antarctica",
	"AS" => "Asia",
	"EU" => "Europe",
	"NA" => "North America",
	"OC" => "Oceania",
	"SA" => "South America"
);

// change user and password with your
// host, user, password, database, [port]
$mysqli = new mysqli("localhost", "user", "password", "myf2b");

if ($mysqli->connect_errno) {
	die('Internal Server Error maybe database');
}

$page = 1;
if(isset($_GET["page"])){
	$page = intval($_GET["page"]);
}

if(isset($_POST["page"])){
	$page = intval($_POST["page"]);
}

//This is the number of results displayed per page 
$page_rows = 15; 
$start = ($page_rows * $page) - $page_rows;

$sql = "select Count(*) AS total FROM kci_logipv4";
$res = $mysqli->query($sql);
$rows = $res->num_rows; 
$total=0;

if($rows) {
	$row = $res->fetch_assoc();
	$total = $row['total'];
}

$sql = "SELECT * FROM kci_logipv4, kci_category WHERE kci_logipv4.kci_category = kci_category.id ORDER BY logdate DESC LIMIT $start, $page_rows ";
$res = $mysqli->query($sql);
$rows = $res->num_rows; 

//This tells us the page number of our last page 
$last = ceil($total/$page_rows);

$line_no=$start;
?>

<html>
<body>
<p>Komunitas Cyber Indonesia <img src="./png/id.png" /></p>
<p>Version: Testing</p>
<p><?php echo $lastphpupdate; // print last update php  ?></p>
<p>Server Farm Info</p>

<?php
if ($res) {
	echo "<p>Total: $total</p>";
	if ($rows>0) {
?>

<table border="1">
<tr>
	<th>No</th>
	<th>Datetime</th>
	<th>IP</th>
	<th>Country</th>
	<th>Code</th>
	<th>Category</th>
	<th>Comment</th>
</tr>

<?php 
		while ($row = $res->fetch_assoc()) {

			$line_no++; // numbering
			// sanitize output
			$logmsg=$row['logmsg'];
			$logmsg=strip_tags($logmsg);
			$logmsg=htmlspecialchars($logmsg);
			$logmsg=nl2br($logmsg);
			$fnameflag="./png/".strtolower($row['codecountry2']).".png";			
?>

<tr>
	<td valign="top"><?php echo $line_no; ?></td>
	<td valign="top"><?php echo $row['logdate']; ?></td>
	<td valign="top" align="center"><?php echo long2ip($row['logipv4']); ?></td>
	<td valign="top" align="center"><?php echo "<img src=\"".$fnameflag."\" />"; ?> <?php echo geoip_country_name_by_name(long2ip($row['logipv4']))."-".$row['codecountry2']."/".$row['codecountry3']; ?></td>
	<td valign="top" align="center"><?php echo $continent[$row['codecontinent']]; ?></td>
	<td valign="top"><?php echo $row['category']; ?></td>
	<td valign="top"><?php echo $logmsg; ?></td>
</tr>

<?php
		}
	} else {
		echo "<p>No Data</p>";
	}
?>
</table>
<?php 
// simple navigation

	if ($last>1) {
		echo "<P>";
		if ($page>1) {
			echo "<a href=\"".htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8")."?page=1\">First</a> ";
		}
		if ($page>2 && $page<=$last) {
			echo "... ";
		}
		if ($page>1) {
			echo "<a href=\"".htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8")."?page=".($page-1)."\">".($page-1)."</a> ";
		}
		echo "$page ";
		if ($page+1<=$last) {
			echo "<a href=\"".htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8")."?page=".($page+1)."\">".($page+1)."</a> ";
		}
		if ($page+1<$last) {
			echo "... ";
		}
		if ($page<$last) {
			echo "<a href=\"".htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8")."?page=$last\">Last</a> ";
		}
		echo "| Number of pages: $last </p>";
	}
} else {
	echo "<p>Query Fail</p>";
}
?>
<p>Hosted on <a href="http://www.aryfanet.com/">Aryfanet Dot Com</a>, your partner to trust.</p>
<p>This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.</p>
<p>Flags from <a href="http://www.famfamfam.com/lab/icons/flags/">http://www.famfamfam.com/lab/icons/flags/</a>.</p>
</body>
</html> 
