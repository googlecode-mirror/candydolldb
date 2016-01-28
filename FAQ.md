# FAQ #




---


## What is the expected direcory-structure for CandyDollDB? ##

The full path of an image in CandyDollDB contains a few components:
<pre>  {CANDYIMAGEPATH}/{FullModelName}/{SetPrefix}{SetName}/{ImageFilename}.{ImageExtension}</pre>
An example path could be (Windows)
<pre>
D:\CandyDoll\Alyona Gromova\set_01\AlyonaG01_001.jpg</pre> or (GNU/Linux)
<pre>
/var/candydoll/Alyona Gromova/set_01/AlyonaG01_001.jpg</pre>


---


## During setup, I keep getting a database-error although all my settings are correct? ##

CandyDollDB versions 1.3 and below did not allow for a blank database-password (as is customary with for example XAMPP, EasyPHP and others). Please update to the [latest version](http://code.google.com/p/candydolldb/downloads/list).


---


## I cannot log in, but no errors are shown! What's wrong? ##

You probably landed onto the login page through a browser bookmark or history entry. The login page normally redirects you to the last page you were on before you had to log in. If that page does no longer exist, or you do not have sufficient permissions to visit that page (again), you get sent back to your previous page - which is the login page.

To resolve this, first log in and when you're on the login page again (without any errors displayed, of course), remove the 'login.aspx?url=...' from your browser address bar. Hit ENTER, and you'll be logged in normally.

Remove or edit the bookmark/history entry if you don't want to do this every time you visit CandyDollDB :-)


---


## Sometimes the page will redirect me to a page I was previously at! What's wrong? ##

Since the pages use a custom referrer, sometimes if you have more than 1 window / tab open showing CDDB pages and click a link in 1 of the windows / tabs. Then go to the other window / tab and click another link, it will use the referrer link that you clicked in the 1st window / tab and load that in the 2nd window /tab once the script is done running

To resolve this, refresh the page before clicking on any new links to make sure the referrer gets updated.


---


## Why is there no 'Remember me' option in the login form? ##

Because the login uses PHP's Session and not Cookies.


---


## What exactly is the 'Thumbnail'-setting during the application's setup? ##

It is a folder inside your CandyDoll-collection where thumbnails of all video's are located. CandyDollDB shows these images on video-related pages.