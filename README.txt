WELCOME:
--------
Welcome to CandyDollDB, a web based way of browsing, updating and
enjoying your CandyDollTV-collection.



DATE & VERSION:
---------------
2012-07-06, v1.7



NEWS:
-----
Do you want to help in developing CandyDollDB, too? Do you need support? 
Don't hesitate to ask and please contact me (see CONTACT). 



SYSTEM REQUIREMENTS:
--------------------
Computer running
 * PHP 5.3
 * MySQL 5.1
 * SMTP-mail server (optional)



FRESH INSTALL:
--------------
 * Extract the downloaded archive into a directory which is accessible 
through your web server.
 * Make sure the user running the web server has read-write access to 
the CandyDollDB root- and cache directories.
 * Make sure the user running the web server has read-access to your 
CandyDoll-collection.
 * Using your browser, navigate to setup.php and follow the on screen 
instructions.
 * NOTE: Any previous CandyDollDB-database will be dropped.
 * After setup is complete, log in and click Features -> Process XML.
 * Import your images and videos per model, per set or on the commandline.
 
  

HISTORY:
--------
2012-07-06 1.7
		Added user-specific dateformats;
		Added user-specific choice between table- and thumbnail views;
		Added the most recent model- and set updates;
		Added tagging of models, sets, images and videos;
		Added search capability based on tagged items, thanks to ErikG;
		Added autosuggest, import and export for tags;
		Added paging for search- and dirty set-pages;
		Added commandline error-output;
		Included an 'offline' copy of jQuery;
		Fixed minor GUI-issues, lots of code clean-up;
		Fixed the skewed portrait-only thumbnail-bug;

2012-02-26 1.6
		Added support for a custom databasename;
		Added the most recent releasedates from the official CD-site;
		Added model thumbnails on main page;
		Added the most probable SvetlanaT06 release-date, thanks to V@RTEX;
		Added ColorBox including slideshow feature, removed SlimBox;
		Added a 'Remarks' field to Models;
		Added a delete-on-overlay feature for deleting cacheimages;
		Added a split pics- and videolist of dirty sets on main page;
		Added functionality for cleaning both cachetable and -folder;
		Fixed the stretching of thumbnail images on portrait-only sets;
		Tweaked the set import to decrease number of dirty sets;
		Tweaked timeout setting for lengthy operations;
		Updated the PHPMailer class;
		Centralized the handling of querystring variables; 
		Changed the inner workings of the random-image grabber;
		Minor cosmetic surgery, bugfixes and code clean up; 

2012-01-28 1.5
		Added a new lay-out and design (Vika is such a beauty, isn't she?);
		Added an admin-panel for accessing functionality found nowhere else;
		Added an ul-li-fied navigation menu;
		Added the CANDYDOLLDB_VERSION constant;
		Added the most recent updates;
		Added physical cache-folder cleaning;
		Refined cache-functionality (auto-delete when appropriate);
		Fixed CLI progress-indicator issue;
		Fixed TimeZone issue - thanks to gothicpinku;
		Fixed the centering of erroroverlay issue;
		Fixed a low-memory issue when downloading large zipfiles;
		Fixed some other minor bugs;
		Removed the alternate style directives (see config.php and setup.php); 		

2012-01-04 1.4
		Added support for a blank database password - thanks to Kasimi;
		Added OS-detection for image- and video-paths during setup;
		Added auto-redirect after session-expiration;
		Added support for requests through HTTPS without unsafe content warnings;
		Added a more elaborate caching mechanism, for models, indexes, sets,
		 videos and images;
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
