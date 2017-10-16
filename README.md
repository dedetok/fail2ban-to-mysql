### fail2ban-to-mysql
PHP

JAVA

### Web Page
http://www.garasiku.web.id/web/kci/kci_logread.php

fail2ban to mysql

### requirement:
1. fail2ban (tested on 0.9.5-1)
2. ipset
3. mysql community edition (tested on 5.7)
4. apache2
5. php (tested on 7.0)

Geoip (geoip-bin geoip-database geoip-database-extra)

	put mlocaldb.conf into /etc/fail2ban/action.d/

# Install php7.0 

	apt-get install php7.0 php7.0-geoip geoip-bin geoip-database geoip-database-extra libapache2-mod-geoip

# mysql

create a new mysql database and it's user to store fail2ban log (you can use kci.sql as references).

# php 

put kci_log.php and kci_logread.php into any user apache html public directory that accesssible via browser. You need to change user name and password to access mysql database. Feel free to use and modify it.

# fail2ban

edit your /etc/fail2ban/jail.conf and add a line to use mlocaldb at the end of action, for example:

	...
	[sshd]
	port = ssh
	logpath = %(sshd_log)s
	backend = %(sshd_backend)s
	enabled = true
	filter = sshd
	action = iptables-ipset-proto4[name=sshd]
	mlocaldb[category=10]
	abuseipdb[category=4,18,22]
	...

restart your fail2ban

# category list:

	id category
	10 SSH
	20 FTP
	30 HTTP/HTTPS
	40 SMTP/POP/IMAP/POP3/S

# ipset list:

	mynetrules hash:net	
	mynetrulesssh hash:net
	mynetruleshttp hash:net
	mynetrulesftp hash:net
	mynetrulessmtp hash:net

# iptables rules:

	-A INPUT -m set --match-set mynetrules src -j DROP
	-A INPUT -p tcp -m multiport --dports 25,465,993,995,465,143,110 -m set --match-set mynetrulessmtp src -j DROP
	-A INPUT -p tcp -m multiport --dports 80,443 -m set --match-set mynetruleshttp src -j DROP
	-A INPUT -p tcp -m multiport --dports 22 -m set --match-set mynetrulesssh src -j DROP
	-A INPUT -p tcp -m multiport --dports 21,22 -m set --match-set mynetrulesftp src -j DROP

# java

java/src/igam contains java program to add ip from mysql into permanent ipset blocked list.

add this java into cron tables /usr/bin/java -jar /root/java/F2BBlock.jar

# flag

download flag from http://www.famfamfam.com/lab/icons/flags/ 

upload png folder into your web folder.


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
