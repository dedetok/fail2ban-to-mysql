# fail2ban-to-mysql
fail2ban to mysql

put mlocaldb.conf into /etc/fail2ban/action.d/
put kci_log.php and kci_logread.php into any user apache html public directory that accesssible via browser. 

edit your /etc/fail2ban/jail.conf and add a line to use mlocaldb at the end of action, for example:
...
[sshd]
port    = ssh
logpath = %(sshd_log)s
backend = %(sshd_backend)s
enabled = true
filter = sshd
action = iptables-ipset-proto4[name=sshd]
        mlocaldb[category=10]
        abuseipdb[category=4,18,22] 
...

restart your fail2ban 

category list:
id	category
10 	SSH          
20 	FTP             
30	HTTP/HTTPS
40	SMTP/POP/IMAP/POP3/S
