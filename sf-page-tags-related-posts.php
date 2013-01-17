<?php
  /*
    Plugin Name: Page Tags & Related Posts 
    Plugin URI: https://github.com/smartfoxes/sf-page-tags-related-posts
    Description: Adds tags meta box to the page and adds widget to display related posts with tags matching page tags.
    Version: 1.0
    Author: Andrei Filonov
    Author URI: http://www.smartfoxes.ca
    License: GPL2
  */

include_once dirname( __FILE__ ) . '/widget.php';

if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';
