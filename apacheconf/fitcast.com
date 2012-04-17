<VirtualHost 72.14.190.37:80>
	ServerName weightcast.com
	ServerAlias *.weightcast.com

	CustomLog /var/log/apache2/fitcast.log combined
	AddDefaultCharset UTF-8

	ServerAdmin webmaster@localhost

	DocumentRoot /var/www/fitcast
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>

	ErrorLog /var/log/apache2/error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn
</VirtualHost>
