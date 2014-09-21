# _Repertoire_

**[GetRepertoire.com](http://getrepertoire.com)**

_Description: Stage-ready sheet music, Chord, tab, and Fake sheet organizer for musicians_

## Features

* Windows, Mac, Linux, and Android support
* IOS support with external server
* PDF, Image, ChordPro, and TXT support
* Search by title, artist, category, ect
* Build and organize set lists
* Accessable to multiple users over local network
* Navigate by mouse, swipe, or go hands-free with an easy to build USB foot pedal

## How to Run

Repertoire is written in PHP and SQLite, for portability, advanced JS based rendering, and cross-platform support.  As such, it requires a webserver, such as Apache, to run.
The most recent version will be available, packaged for multiple operating systems, at [GetRepertoire.com](http://getrepertoire.com).

### Package it Yourself

#### Windows

1. _Download and unzip [USB Webserver](http://usbwebserver.net)_
2. _Delete the files in the `./root` directory, and replace with the contents of Repertoire_
4. _Run USBWebserver.exe_
5. _Open your browser to http://localhost:8080 to start Repertoire_

#### Mac (Untested)

1. _Download and install [MAMP](http://www.mamp.info/en/) webserver
2. _Copy Repertoire into your MAMP root directory_
3. _Run MAMP_
4. _Click "Open Start Page" to start Repertoire_

#### Linux

1. _Install and start a LAMP stack with SQLite3 support (seriously, if you use Linux, you don't need my help with this)_
2. _Copy Repertoire into your server's root directory_
3. _Open the localhost in your browser, at whatever port you configured, to start Repertoire_

#### Android

1. _Install [Server for PHP](https://play.google.com/store/apps/details?id=com.esminis.server.php&hl=en), from the Play Store_
2. _Connect your device via USB, in 'Mass Storage Mode'_
3. _Copy Repertoire into your `./www` folder_
4. _Start Server for PHP_
5. click the link at the bottom (usually starts with http://127.0.0.1), to open Repertoire in your browser

## Change Log

09/21/2014 - **V0.8a** - Beginnings of unified AJAX controller, Completed PDF renderer
09/14/2014 - **V0.7a** - Initial public release

## License

Repertoire is GPL licensed.  It makes use of several 3rd party libraries, with their own compatible 
licenses.  Some portions of the pre-packaged configurations may include closed source or alternatively licensed code.

## Donate

Repertoire is a huge project, that I tend to support and grow for years to come.  If you enjoy it, please consider 
donating to the continued development of the system at [GetRepertoire.com/Donate](http://getrepertoire.com/Donate)