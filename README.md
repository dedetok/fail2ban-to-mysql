### fail2ban-to-mysql
PHP

JAVA

### Web Page
http://garasiku.web.id

fail2ban to mysql

### requirement:
fail2ban (tested on 0.9.5-1)

ipset

mysql (tested on 5.5)

apache2

php (tested on 5.6)

Geoip (geoip-bin geoip-database geoip-database-extra)

put mlocaldb.conf into /etc/fail2ban/action.d/

### Install php7.0 

pt-get install php7.0 php7.0-geoip geoip-bin geoip-database geoip-database-extra libapache2-mod-geoip

### mysql
create a new mysql database and it's user to store fail2ban log (you can use kci.sql as references).

### php 
put kci_log.php and kci_logread.php into any user apache html public directory that accesssible via browser. You need to change user name and password to access mysql database. Feel free to use and modify it.

### fail2ban
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

### category list:
id category

10 SSH

20 FTP

30 HTTP/HTTPS

40 SMTP/POP/IMAP/POP3/S

### ipset list:
mynetrules hash:net

mynetrulesssh hash:net

mynetruleshttp hash:net

mynetrulesftp hash:net

mynetrulessmtp hash:net

### iptables rules:
-A INPUT -m set --match-set mynetrules src -j DROP

-A INPUT -p tcp -m multiport --dports 25,465,993,995,465,143,110 -m set --match-set mynetrulessmtp src -j DROP

-A INPUT -p tcp -m multiport --dports 80,443 -m set --match-set mynetruleshttp src -j DROP

-A INPUT -p tcp -m multiport --dports 22 -m set --match-set mynetrulesssh src -j DROP

-A INPUT -p tcp -m multiport --dports 21,22 -m set --match-set mynetrulesftp src -j DROP

### java
java/src/igam contains java program to add ip from mysql into permanent ipset blocked list.

add this java into cron tables /usr/bin/java -jar /root/java/F2BBlock.jar

### flag
download flag from http://www.famfamfam.com/lab/icons/flags/ 

upload png folder into your web folder.
