<?php 
session_start();
define("kci",1);

/*
CREATE 
VIEW kci_view_by_date_month_year
AS
SELECT YEAR(`kci_logipv4`.`logdate`) AS `kci_year`, MONTH(`kci_logipv4`.`logdate`) AS `kci_month`,  DAYOFMONTH(`kci_logipv4`.`logdate`) AS `kci_date`, `kci_logipv4`.`kci_category` AS `kci_category`, COUNT(`kci_logipv4`.`id`) AS `kci_count` FROM `kci_logipv4` GROUP BY YEAR(`kci_logipv4`.`logdate`),MONTH(`kci_logipv4`.`logdate`),DAYOFMONTH(`kci_logipv4`.`logdate`), `kci_logipv4`.`kci_category`

table: kci_category
id int(11)
category varchar(20)

| 10 | SSH                  |
| 20 | FTP                  |
| 30 | HTTP/HTTPS           |
| 40 | SMTP/POP/IMAP/POP3/S | 
*/

$year =0;
$month=0;

if(isset($_GET["year"]) && isset($_GET["month"])){
	$year = intval($_GET["year"]);
	$month = intval($_GET["month"]);
}

if(isset($_POST["year"]) && isset($_POST["month"])){
	$year = intval($_POST["year"]);
	$month = intval($_POST["month"]);
}

$lastphpupdate = "last update 2017-02-04 09:40 \n"; // info last update php 

// host, user, password, database, [port]
$mysqli = new mysqli("localhost", "user", "password", "myf2b");

if ($mysqli->connect_errno) {
	die('Internal Server Error maybe database');
}
$sql = "SELECT * FROM kci_view_by_date_month_year ORDER BY  kci_year desc, kci_month desc, kci_date desc, kci_category asc";
if ($year>0 && $month>0) {
	$sql = "SELECT * FROM kci_view_by_date_month_year WHERE kci_year=$year AND kci_month=$month ORDER BY  kci_year desc, kci_month desc, kci_date desc, kci_category asc";
}
$res = $mysqli->query($sql);
$rows = $res->num_rows; 

?>
<html>
<head>
</head>
<body>
<?php 
// body header 
include_once("./kci_body_header.php");
if ($rows >0) {
?>
	<table border="1">
	<tr>
		<th>No</th>
		<th>Year</th>
		<th>Month</th>
		<th>Date</th>
		<th>Category</th>
		<th>Number</th>
	</tr>
<?php 
	$line_no=0;
	while ($row = $res->fetch_assoc()) {
		$line_no++; // numbering
		// sanitize output
		$kci_year=intval($row['kci_year']);
		$kci_month=intval($row['kci_month']);
		$kci_date=intval($row['kci_date']);
		$kci_category=intval($row['kci_category']);
		$kci_count=intval($row['kci_count']);
		$kci_cat = "";
		switch ($kci_category) {
			case 10:
				$kci_cat="SSH";
				break;
			case 20:
				$kci_cat="FTP";
				break;
			case 30:
				$kci_cat="HTTP/HTTPS";
				break;
			case 40:
				$kci_cat="SMTP/POP/IMAP/POP3/S";
				break;
		}
?>
	<tr>
		<td valign="top"><?php echo $line_no; ?></td>
		<td valign="top"><?php echo $kci_year; ?></td>
		<td valign="top"><?php echo $kci_month; ?></td>
		<td valign="top"><?php echo $kci_date; ?></td>
		<td valign="top"><?php echo $kci_cat; ?></td>
		<td valign="top" align="center"><?php echo $kci_count; ?> </td>
	</tr>
<?php
	}
?>
	</table>
<?php 
} else {
	echo "<p>No Data</p>";
}
// body bottom 
include_once("./kci_body_bottom.php");
?>
</body>
</html>
