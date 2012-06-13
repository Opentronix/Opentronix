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
  To install Opentronix on your webserver, upload the contents
  of the "upload/" folder to the preferred location on your webserver
  (wherever you want to install Opentronix) with your favorite FTP client.
  Open with your browser the "install" location in this folder and follow
  the steps in the installation wizard.
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
