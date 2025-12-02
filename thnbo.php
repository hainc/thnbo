<?php
/**
 * Plugin Name: 缩略图美化
 * Description: 一款针对WordPress开发的缩略图美化插件,为广大站长提供缩略图的美化便利。
 * Author: 源分享
 * Author URI:https://www.yfx.top
 * Version: 1.8
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// 检查PHP版本
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        ?>
        <div class="error notice">
            <p><?php _e('缩略图美化插件需要PHP 7.4或更高版本才能运行。当前PHP版本为：' . PHP_VERSION, 'thnbo'); ?></p>
        </div>
        <?php
    });
    return;
}

define( 'BASENAME', plugin_basename( __FILE__ ) );

add_action( 'wp_loaded', function () {
    /** 装载插件 */
    require_once 'load.php';
} );