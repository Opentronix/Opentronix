Welcome to Opentronix
  -------------------------
  Opentronix is a multimedia microblogging platform. It helps
  people in a community, company, or group to exchange short messages over
  the Web. Find more information in http://...
  -------------------------

License
  -------------------------
  Please check out the license.txt file. By installing Opentronix, you
  agree to all the conditions of the license and also to the Opentronix
  Terms of Use: http://...
  -------------------------

INSTALLATION
  -------------------------
  The following steps describe the installation on a basic Debian Wheezy
  system, but might also be adopted to any other compatible distribution.

  Please copy and paste the lines with trailing \ as a block. Let's try it
  with the following one. It should make sure all packages listed in the
  two lines below, are going to be installed:

  apt-get install apache2 mysql-server mysql-client php5 \
   libapache2-mod-php5 php5-gd php5-mysql unzip

  mysql -p

  Enter the password, that you have defined during installation. Within the
  MySQL console, execute the following commands, but replace YourPassword
  that you are planning to use for your database:

  CREATE DATABASE opentronix;
  CREATE USER 'opentronix'@'localhost' IDENTIFIED BY 'YourPassword';
  GRANT ALL PRIVILEGES ON opentronix . * TO 'opentronix'@'localhost';
  FLUSH PRIVILEGES;
  exit

  a2enmod rewrite

  sed -ie 's/AllowOverride None/AllowOverride All/' /etc/apache2/sites-enabled/000-default

  wget https://github.com/Opentronix/Opentronix/archive/master.zip -O /var/www/master.zip

  unzip -d /var/www/ /var/www/master.zip && mv /var/www/Opentronix-master/upload/* \ 
   /var/www/ && rm -rf /var/www/Opentronix-master/

  touch /var/www/.htaccess && chmod 766 /var/www/.htaccess

  for i in /var/www/themes/ /var/www/i/attachments/ /var/www/i/avatars/thumbs1/ \ 
   /var/www/i/avatars/thumbs2/ /var/www/i/avatars/thumbs3/ /var/www/i/avatars/ \
   /var/www/i/tmp/ /var/www/system/cache/ /var/www/system; do mkdir -p $i \
   && chmod 766 $i; done

  find /var/www/ -exec chown www-data:www-data {} \;

  Open http://YourFQDN/install or http://YourIPAddress/install where you have to replace
  YourFQDN with the resolvable hostname or YourIPAddress with the IP Address of your host.

  During the installation process, please select a proper caching system. FileSystem Storage
  does not require any additional configuration, so that might be the easiest to setup,
  though it's not the fastest one.

  Define http://YourIPAddress or http://YourFQDN as Website Address.

  -------------------------

UPGRADE
  -------------------------
  To upgrade Opentronix from a previous version, just follow
  the Installation steps. Replace the old installation files with the
  contents of the "upload/" folder and run the installation wizard. But
  first - don't forget to backup your old installation (database and files)
  - it's important!
  -------------------------

System Requirements
  -------------------------
  - Apache Web Server
  - MySQL version 5.0 or higher
  - PHP version 5.1 or higher
  -------------------------

Official website
  -------------------------
  http://...
  -------------------------

FACEBOOK CONNECT
  -------------------------
  To activate Facebook Connect integration for your Opentronix site, first
  you have to register a Facebook application and get its API key:
  1. Complete the Opentronix installation/upgrade script
  2. Go to FB and join the Developers group: http://facebook.com/developers
  3. Create new application: http://facebook.com/developers/createapp.php
  4. Go to the application and click "Edit Settings"
  5. From the "Connect" tab fill the fields "Connect URL" and "Base Domain"
  6. From the "Advanced" tab fill the field "Email Domain"
  7. Place the API Key in ./system/conf_main.php in $C->FACEBOOK_API_KEY
  -------------------------
  
TWITTER CONNECT
  -------------------------
  To activate Twitter OAuth Login for your Opentronix site, first you have
  to register a Twitter application and get its Consumer KEY and SECRET:
  1. Complete the Opentronix installation/upgrade script
  2. Go to the Twitter New Application form: http://twitter.com/apps/new
  3. For "Application Type" choose "Browser"
  4. For "Callback URL" enter http://your-opentronix-url/twitter-connect
  5. For "Default Access type" choose "Read & Write"
  6. Select che "Use Twitter for login" checkbox
  7. Submit the form, get the "Consumer key" and "Consumer secret" and then
     place them in ./system/conf_main.php - in $C->TWITTER_CONSUMER_KEY and
     $C->TWITTER_CONSUMER_SECRET
  -------------------------
  
YAHOO: Inviting contacts from Yahoo
  -------------------------
  To activate the Yahoo page in the Invitation center, first you have to 
  register a Yahoo application and get its Consumey KEY and SECRET:
  1. Complete the Opentronix installation/upgrade script
  2. Go to the Yahoo New App form: https://developer.apps.yahoo.com/projects
  3. For Type of applcation choose "Create apps that use Yahoo! OAuth APIs"
  4. On the next step for "Kind of Application" choose "Web-based"
  5. For "Application Domain" fill your Opentronix site url
  6. For "Access Scopes" choose "This app requires access to private user data"
  7. From the menus choose "Read Full" for the "Yahoo! Contacts" section
  8. Submit the form, get the "Consumer key" and "Consumer secret" and then
     place them in ./system/conf_main.php - in $C->YAHOO_CONSUMER_KEY and
     $C->YAHOO_CONSUMER_SECRET
  -------------------------
