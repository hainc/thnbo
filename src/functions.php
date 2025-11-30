<?php
/**
 * 公共函数库
 *
 * @package THNBO
 */

use THNBO\Src\Cut_Theme;
use THNBO\Src\Cut_Type;

/**
 * 获取选项
 *
 * @param string $name 选项名，为空时返回本插件所有选项否则返回具体的选项值
 *
 * @return array|mixed
 */
function thnbo_get_option( string $name = '' ) {
    $thnbo_options = get_option( 'thnbo_options' );
    $defaults      = array(
        'cut_type'  => Cut_Type::CENTER,
        'cut_theme' => Cut_Theme::RESOURCE,
    );

    if ( ! empty( $name ) ) {
        $options = wp_parse_args( $thnbo_options, $defaults );
    return $options[ $name ];
    }

    return wp_parse_args( $thnbo_options, $defaults );
}