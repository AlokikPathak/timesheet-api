<?php
/**
 * File Name : config.php
 * Contains data to establish connection  with FileMaker Server
 *  
 * @author : Alokik Pathak
 */

define('FM_HOST', getenv('FM_HOST'));
define('FM_FILE', getenv('FM_FILE'));
define('FM_USER', getenv('FM_USER'));
define('FM_PASSWORD', getenv('FM_PASSWORD'));
define('LAYOUT_USERS', 'Users_USR');
define('LAYOUT_ACTIVITY', 'Activity_ATY');
 ?>
