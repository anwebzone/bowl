BOWL - Frågeforum
=========

Bowl är ett frågeforum för bowlingintresserade. Syftet är öka bowlingintresset samt sprida kunskap om sporten. Vem som helst kan ställa frågor och besvara andras frågor, allt man behöver är ett konto.


###Installation

Du kan enkelt klona en egen version av detta repository genom att använda:
> git clone https://github.com/anwebzone/bowl.git

Alternativt ladda ner direkt här:
> https://github.com/anwebzone/bowl/archive/master.zip

För att "snygga länkar" ska fungera kan du behöva ändra sökvägen för RewriteBase i .htaccess som du hittar i mappen webroot.
> RewriteBase /bowl/webroot/


####MySQL databas behövs
För att installera en egen version av BOWL behöver du tillgång till en egen MySQL databas. 
I nerladdningen av BOWL finns det en SQL fil med alla tabeller som behövs för att BOWL ska fungera.

> sql/bowl.sql

Du måste importera bowl.sql genom phpMyAdmin eller MySQL command line.


###Bowl Build

Bowl är baserat på Anax MVC ett micro ramverk skapat i PHP. 

Du kan läsa lite mer om det här:

["Anax som MVC-ramverk"](http://dbwebb.se/kunskap/anax-som-mvc-ramverk) 
 
["Bygg en me-sida med Anax-MVC"](http://dbwebb.se/kunskap/bygg-en-me-sida-med-anax-mvc). 

["Anax - en hållbar struktur för dina webbapplikationer"](http://dbwebb.se/kunskap/anax-en-hallbar-struktur-for-dina-webbapplikationer) för none-MVC varianten. 

Ramverket är utvecklat av Mikael Roos, me@mikaelroos.se.


License 
------------------

This software is free software and carries a MIT license.



Use of external libraries
-----------------------------------

The following external modules are included and subject to its own license.



### Modernizr
* Website: http://modernizr.com/
* Version: 2.6.2
* License: MIT license 
* Path: included in `webroot/js/modernizr.js`



### PHP Markdown
* Website: http://michelf.ca/projects/php-markdown/
* Version: 1.4.0, November 29, 2013
* License: PHP Markdown Lib Copyright © 2004-2013 Michel Fortin http://michelf.ca/ 
* Path: included in `3pp/php-markdown`










