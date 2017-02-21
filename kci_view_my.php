<?php 
/*
CREATE 
VIEW kci_view_by_month_year
AS
SELECT YEAR(`kci_logipv4`.`logdate`) AS `kci_year`,MONTH(`kci_logipv4`.`logdate`) AS `kci_month`,`kci_logipv4`.`kci_category` AS `kci_category`, COUNT(`kci_logipv4`.`id`) AS `kci_count` FROM `kci_logipv4` GROUP BY YEAR(`kci_logipv4`.`logdate`),MONTH(`kci_logipv4`.`logdate`),`kci_logipv4`.`kci_category`

demo http://www.garasiku.web.id/web/kci/kci_view_my.php

table: kci_category
id int(11)
category varchar(20)

| 10 | SSH                  |
| 20 | FTP                  |
| 30 | HTTP/HTTPS           |
| 40 | SMTP/POP/IMAP/POP3/S | 
*/

$lastphpupdate = "last update 2017-02-04 09:40 \n"; // info last update php 
// host, user, password, database, [port]
$mysqli = new mysqli("localhost", "user", "password", "myf2b");
if ($mysqli->connect_errno) {
	die('Internal Server Error maybe database');
}
$sql = "SELECT * FROM kci_view_by_month_year ORDER BY  kci_year desc, kci_month desc, kci_category asc";
$res = $mysqli->query($sql);
$rows = $res->num_rows; 

?>
<html>
<head>
</head>
<body>
<p>Komunitas Cyber Indonesia <img src="./png/id.png" /></p>
<p>Version: Alpa</p>
<p><?php echo $lastphpupdate; // print last update php  ?></p>
<p>Server Farm Info</p>
<?php 
if ($rows >0) {
?>
	<table border="1">
	<tr>
		<th>No</th>
		<th>Year</th>
		<th>Month</th>
		<th>Category</th>
		<th>Number</th>
	</tr>
<?php 
	while ($row = $res->fetch_assoc()) {
		$line_no++; // numbering
		// sanitize output
		$kci_year=intval($row['kci_year']);
		$kci_month=intval($row['kci_month']);
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
?>
</body>
</html>
