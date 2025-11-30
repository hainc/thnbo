<?php
/**
 * 插件装载文件
 *
 * @package THNBO
 */

/** 载入Composer的自动加载程序 */

use THNBO\Src\Core;

require_once 'vendor/autoload.php';
require_once 'lib/grafika/src/autoloader.php';

/** 载入配置文件 */
require_once 'config.php';

/** 载入公共函数 */
require_once 'src/functions.php';

/** 载入设置页 */
require_once 'src/setting.php';

/** 注册静态脚本 */
require_once 'src/enqueue-scripts.php';

/** 加载核心功能 */
$thnbo_options = thnbo_get_option();
$core          = new Core();
$core->set_cut_type( $thnbo_options['cut_type'] );
$core->set_cut_theme( $_POST['cut_theme'] ?? $thnbo_options['cut_theme'] );
$core->register_hook();
