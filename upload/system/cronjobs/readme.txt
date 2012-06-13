Opentronix needs some scripts to be ran in background periodically.

This can be done very well with Cronjobs, but the problem is that most
of the hosting providers don't support cronjobs. So Opentronix uses
very ugly alternative which can cause slow page load sometimes.

If your server supports cronjobs, you can fix this by setting the
following cronjob to run every minute with the linux crontab command: 

* * * * *  /path/to/cli/php /path/to/stx/system/cronjobs/worker.php

In this line /path/to/cli/php is the path where PHP CLI is installed
(PHP Command Line Interface).
/path/to/stx is the location of your Opentronix installation.

After you install this cronjob, open the Opentronix config file:
/path/to/stx/system/conf_main.php
and change the value of $C->CRONJOB_IS_INSTALLED to TRUE. If this
variable doesn't exist, add it.

If you're not a technical person and you're not sure what to do,
don't do anything :)
