<?php
/**
 * Plugin Name: 缩略图美化
 * Description: 一款针对WordPress开发的缩略图美化插件,为广大站长提供缩略图的美化便利。
 * Author: 源分享
 * Author URI:https://www.yfx.top
 * Version: 1.5
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'BASENAME', plugin_basename( __FILE__ ) );

add_action( 'wp_loaded', function () {
    /** 装载插件 */
    require_once 'load.php';
} );
