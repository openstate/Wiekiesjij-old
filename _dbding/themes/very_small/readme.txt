
    README FILE FOR THE VERY SMALL THEME USED WITH PHPMAYADMIN
----------------------------------------------------------------------

CHANGE LOG:
    - 2005-09-12;
      > Supporting phpMyAdmin Version 2.6.4 and higher
    - 2005-09-11:
      > $pma_http_url = '';
        set here your absolute url to the theme
        directory (if required)

1. INSTALLATION
----------------------------------------------------------------------
   Simply unzip the files.
   (sample: [whatever]/phpMyAdmin/themes/)

   One each .css you'll find in first line <?php $pma_http_url = ''; ?>.
   Here you can (if required) the url to the 'artic_ocean' theme.
   This may fix some problems with relative urls.

   Then make sure, that all images are in the directory
   - [whatever]/phpMyAdmin/themes/arctic_ocean/img/

   and all *.css.php files are in the directory
   - [whatever]/phpMyAdmin/themes/arctic_ocean/css/.

   The two *.inc.php files must stored in the directory
   - [whatever]/phpMyAdmin/themes/arctic_ocean/.
			
  Note:
    [whatever] is any path to your phpMyAdmin-Installation.

----------------------------------------------------------------------


2. REQUIREMENTS / INFORMATIONS
----------------------------------------------------------------------
   - phpMyAdmin Version 2.6.2 or higher
   - full CSS2 compatible browser
     (I've tested with Firefox 1.02, Microsoft(R) 
      InternetExplorer 6.0, and Opera 7.54)
   - Your browser should support Javascript
     and png-images.
   - In phpMyAdmin Version 2.6.4 there's
     a new navigation behavior included.
     The navigation panel has a fixed position now.

----------------------------------------------------------------------


3. INFORMATION ABOUT THE ARCTIC-OCEAN THEME:
----------------------------------------------------------------------
   a) ICONS:
      Database Icon-Set made 2005 by Michael Keck.
      Please see license.txt file for more informations.
   b) THEME:
      The theme is based on the 'arctic ocean' theme by Michael Keck 
	  which is based on the 'darkblue_orange' theme made by the
      members of the phpMyAdmin Team.
      Modification and integration of the 'darkblue_orange' theme and
      the new Database Icon-Set is made by
      Michael Keck and Ruben Barkow.
