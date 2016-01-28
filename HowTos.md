# HowTo's #



## Setup ##

To setup the application, proceed as follows:

  1. [Download](http://code.google.com/p/candydolldb/downloads/list) a public release of CandyDollDB, or check out a the most current release from [SubVersion](http://code.google.com/p/candydolldb/source/checkout).
  1. Place the application in a directory accessable through a webserver (e.g. `/var/www/candydolldb/` or `C:\xampp\htdocs\candydolldb`).
  1. Make sure that the user running the webserver has sufficient read- and or writepermissions in the application's root- and cache directories. Don't forget read-permission for your CD-collection, too.
  1. Check if the configuration-file (`config.php`) already exists. If it does, remove it.<br>All subsequent page-requests will be redirected to <code>setup.php</code>.<br>
<ol><li>Enter all the required data, e.g. database connection data, user account data, SMTP connection data.<br>If all goes well, you should then be prompted to log in.</li></ol>

Meanwhile, the file <code>config.php</code> has been written to your installation directory.<br>It contains all the settings you provided, and a few more.<br>
<br>
Among those are the e-mailtemplate to use when a user forgot her/his password and the user-id of the first user to be setup; this will be used for commandline-authentication (see section <a href='#Batch-imports.md'>Batch-imports</a>).<br>
<br>
Once you are logged in, you can start importing models and sets:<br>
<br>
<ol><li>Make sure you use the most up-to-date XML-file with all model- and setdata.<br>The XML-file can be downloaded from the <a href='http://code.google.com/p/candydolldb/downloads/list'>Downloads</a>-page.<br>
</li><li>Navigate to 'Import XML' in the Features-menu and choose the downloaded file. Click 'Import XML' and wait a while.<br>
</li><li>Now you can import images and videos into their corresponding sets.<br>You can either<br>
<ul><li>do it manually (great fun, if you've got some time to kill), or<br>
</li><li>do it automatically (see section <a href='#Batch-imports.md'>Batch-imports</a>).</li></ul></li></ol>

<h2>Batch-imports</h2>

Because of the long duration of an all-images-all-videos-import, it is best to do this on the commandline of your server. In the next examples, the application is located in /var/www/candydolldb/ on a GNU/Linux system with PHP5.<br>
<br>
Open a terminal and enter the following command to import all images:<br>
<br>
<pre><code>$ php5 -f /var/www/candydolldb/import_image.php<br>
</code></pre>

For importing videos, use the following:<br>
<br>
<pre><code>$ php5 -f /var/www/candydolldb/import_video.php<br>
</code></pre>

<h2>Regular updates</h2>

<ol><li>Make sure you use the most up-to-date XML-file with all model- and setdata. The XML-file can be downloaded from the <a href='http://code.google.com/p/candydolldb/downloads/list'>Downloads</a>-page and must be renamed to <code>setup_data.xml</code>. It contains the most complete list of models, their first- and lastnames, birthdates, sets and all releasedates.<br>
</li><li>Click the 'Process XML-data' button in the Features-menu.<br>This can take a while. Alternatively, you can import the data via the commandline:<br>
<pre><code>$ php5 -f /var/www/candydolldb/setup_data.php<br>
</code></pre></li></ol>

<h2>Validate a CandydollDB XML-file</h2>

To validate an XML-file for use in CandydollDB, one can use for example the <a href='http://xmlsoft.org/'>xmllint</a> utility on GNU/Linux.<br>
<br>
<pre><code>$ xmllint --noout --schema /path/to/candydolldb-schema.xsd /path/to/xml-file.xml<br>
</code></pre>

This will either report success or failure.<br>
<br>
<h2>Recreate all model-, index- and set-cacheimages</h2>

When you want to refill your cache folder after, for example, deleting it by mistake, you can use GNU wget to do the trick almost automatically.<br>
<br>
<ol><li>Hack the Authentication class (class.global.php) to always return a valid user.<br>
</li><li>Then call the following shell-command, where ./cddb/ is the folder where all files will be stored and <a href='http://localhost/candydolldb/'>http://localhost/candydolldb/</a> the URL of your CandyDollDB.<br>
<pre><code>$ wget -erobots=off --mirror --page-requisites --accept *download_image.php*,*set.php* --reject *import* --directory-prefix=./cddb/ http://localhost/candydolldb/<br>
</code></pre>
</li><li>Remove the folder where wget stored the files.<br>
</li><li>Unhack the Authentication class.