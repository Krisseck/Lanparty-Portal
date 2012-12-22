Lanparty Portal
===============

**Lanparty Intranet, based on CodeIgniter**

** WARNING: This is still considered Alpha! Do NOT install this if you are not proficient with LAMP/WAMP etc and if you are not comfortable working with MySQL interfaces such as PHPMyAdmin!**

Demo: <http://demointra.urbanlan.fi> username: admin, password: demo

Features
--------

* Seat registration system
* Schedule
* Seat/IP Address Tracking
* Tournament participation system (via BinaryBeast)
* Song requests (with/without Spotify)
* IRC (via Quakenet)
* Now playing info (via Last.FM)
* Static pages (map, pizza menus)

Technologies used
------------------

* [CodeIgniter](http://ellislab.com/codeigniter)
* [phpass](http://www.openwall.com/phpass/)
* [Phil Sturgeon's Template Library](https://github.com/philsturgeon/codeigniter-template)
* [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* [jQuery](http://jquery.com/)
* [Font Awesome](http://fortawesome.github.com/Font-Awesome/)
* [Elliot Haughin's codeigniter-lastfm (modified)](https://github.com/programmieraffe/codeigniter-lastfm)
* [BinaryBeast API (custom library)](http://binarybeast.com/api)

Server Requirements
-------------------

* Apache 2.2.X
* PHP >= 5.3
* MySQL 14.X
* CURL

Installation
------------


**Copying the files**

* Copy the files to your web server of choice. In the package, there is the folder "public_html". This needs folder needs to point at your website's www-folder, usually also named "public_html".

**Database**

* Create database with a MySQL interface, like PHPMyAdmin
* Run database_structure.sql to the database

**Core files**

* Rename public_html/index.php.default to public_html/index.php and edit if needed
* Rename public_html/.htaccess.default to public_html/.htaccess and edit if needed

**Config files**

* Rename application/config/Binarybeast.php.default to application/config/Binarybeast.php and edit if needed
* Rename application/config/lastfm.php.default to application/config/lastfm.php and edit if needed
* Rename application/config/config.php.default to application/config/config.php and edit if needed
* Rename application/config/database.php.default to application/config/database.php and input your database credentials

**Data generation**

* With your web browser, go to the page http://yourlanpartyurl/install/generate_seats/*pattern*

Replace *pattern* with the table layout of your lanparty. The program will generate seats with a letter-prefix and number-suffix. Separate the suffix and prefix with "-" character and tables with ":" character.

Example: Say you have two tables at your lanparty. First table has 20 seats, and the second one has 30 seats. So the pattern for these tables is: **A-20:B-30**.

This will generate two tables. First one starts with seat A1, then A2, A3... to A20. Then the next table will have seats B1, B2... to B30.


* With your web browser, go to the page http://yourlanpartyurl/add_admin/*username*/*password*

Replace *username* with your admin username and *password* with your admin password.

* Remove the file application/controllers/install.php


**Finished!**

Your lanparty portal should now be running, congratulations! These is still some configuration needed to be done for the site to function correctly.

* In the database, input events to your "schedule"-table. Name of the event, Description, Start time & End time
* In the database, input tournaments to your "compos"-table. Title of tournament, BinaryBeast-code, Description and Weight in which order to show these tournaments.
* In application/controllers/dashboard.php, line 126, change "#changeme" to your IRC channel on Quakenet.
* With your web browser, go to the page http://yourlanpartyurl/fill to print your codes for lanparty goers (Example: <http://demointra.urbanlan.fi/fill>)


For the participants
--------------------

For the site to function correctly, the participants of the lanparty need to register to the website using the codes they are given (these can be printed at http://yourlanpartyurl/fill)

It is important that the participants register using their own computer at the lanparty, since this system will attach that IP address to that user on the database.


TODO
----

* **Much better installation**
* Admin-interface for everything
* Better documentation
* Feature showcase
* Pizza ordering system
* Better usage of BinaryBeast API (tournament registration, participation etc)