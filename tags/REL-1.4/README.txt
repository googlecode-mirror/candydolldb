WELCOME:
--------
Welcome to CandyDollDB, a web based way of browsing, updating and
enjoying your CandyDollTV-collection.



DATE & VERSION:
---------------
2012-01-04, v1.4



NEWS:
-----
Lots of new features and fixes, thanks to a lot of feedback!
Do you want to help building CandyDollDB? Do you need support? 
Don't hesitate to ask and please contact me (see CONTACT). 



SYSTEM REQUIREMENTS:
--------------------
Computer running
 * PHP 5.3
 * MySQL 5.1
 * SMTP-mail server (optional)



FRESH INSTALLATION:
-------------------
 * Extract the downloaded archive into a directory which is accessible 
through your web server.
 * Make sure the user running the web server has read-write access to 
the CandyDollDB root- and cache directories.
 * Make sure the user running the web server has read-access to your 
CandyDoll-collection.
 * Using your browser, navigate to setup.php and follow the on screen 
instructions.
 * NOTE: Any previous CandyDollDB-database will be dropped.



UPDATE INSTALLATION FROM v1.3:
------------------------------
 * Extract the downloaded archive into the directory in which you
 installed CandyDollDB, overwriting all files in the process.
 * In your browser, navigate to setup_v13-v14.php and click 'yes'.
 * NOTE: The CacheImage table will be dropped before being recreated.



HISTORY:
--------
2012-01-04 1.4
		Added support for a blank database password - thanks to Kasimi;
		Added OS-detection for image- and video-paths during setup;
		Added auto-redirect after session-expiration;
		Added support for requests through HTTPS without unsafe content warnings;
		Added a more elaborate caching mechanism, for models, indexes, sets, videos and images;
		Added thumbnailed set overview instead of a simple table; 
		Added a multi-download form for custom-built zip downloads;
		Added XML-export functionality for models and sets;
		Added a very big list of model birthdates - thanks to CandyGirl;
		Added a very big list of releasedates - thanks to Archive.org;
		Fixed a potential security hole: exit after header(location); 
		Tons of code clean-up and minor cosmetic surgery;

2011-09-25 1.3
		Added an automatic index generator;
		Added a caching mechanism for this generator;
		Added lots of new models and release-dates;
		Added convenient filenames for zip-exports;
		Added dates and -search capability on dirty sets;
		Added an alternate style (i.e. optional override);
		Added a password generator;
		Minor bugfix in importscript;
		Minor UI-fix in focussing text inputs;

2011-07-21 1.2
		SQL-user bug fixed in setup-script;
		Multiple 'undefined'-bugs in imports fixed;

2011-07-18 1.1
		Added support for multiple image- or videodates;
		Added extra verification during password-resets;
		Fixed a password-reset bug;
		Added some eyecandy during image-loads;
		Added a progressindicator to commandline imports;
		Added a few LightBoxes where appropriate;
		Database renamed, setupscript rewritten;
		Updated list of release-dates (as of 2011-07-15);
		Lots of code clean-up and minor cosmetic surgery; 

2011-07-04 1.0
		First public release.



CONTACT:
--------
Name:	FWieP (main developer)
Email:	fwiep@fwiep.nl
WWW:	https://code.google.com/p/candydolldb/
SVN:	http://candydolldb.googlecode.com/svn/trunk/
