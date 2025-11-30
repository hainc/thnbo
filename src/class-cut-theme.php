<?php
/**
 * 主题静态类文件
 */

namespace THNBO\Src;

class Cut_Theme {
    
    /**
     * 关闭美化
     */
    const CLOSE = 'close';
    
    /**
     * 资源主题
     */
    const RESOURCE = 'resource';

    /**
     * 素材主题
     */
    const MATERIAL = 'material';

    /**
     * 文章主题
     */
    const POST = 'post';

    /**
     * 海报主题
     */
    const BILL = 'bill';

    /**
     * 主题文件
     */
    const THEME_FILE = array(
        self::RESOURCE => array(
            'tpls'     => array(
                'tpl/1/tpl_1.png',
                'tpl/1/tpl_2.png',
                'tpl/1/tpl_3.png',
                'tpl/1/tpl_4.png',
                'tpl/1/tpl_5.png',
                'tpl/1/tpl_6.png',
            ),
            'width'    => 280,
            'height'   => 167,
            'position' => 'center',
            'offset_x' => 0,
            'offset_y' => 3,
        ),
        self::MATERIAL => array(
            'tpls'     => array(
                'tpl/2/tpl_1.png',
            ),
            'width'    => 280,
            'height'   => 170,
            'position' => 'top-center',
            'offset_x' => 0,
            'offset_y' => 35,
        ),
        self::POST     => array(
            'tpls'     => array(
                'tpl/3/tpl_1.png',
            ),
            'width'    => 265,
            'height'   => 160,
            'position' => 'top-center',
            'offset_x' => 0,
            'offset_y' => 20,
        ),
        self::BILL     => array(
            'tpls'     => array(
                'tpl/4/tpl_1.png',
                'tpl/4/tpl_2.png',
            ),
            'width'    => 315,
            'height'   => 415,
            'position' => 'top-center',
            'offset_x' => 0,
            'offset_y' => 3,
        ),
    );

    /**
     * 根据主题名返回主题的元信息
     *
     * @param string $theme 主题名
     *
     * @return array 主题的元信息
     */
    public static function get_theme_meta( string $theme ): array {
        return self::THEME_FILE[ $theme ];
    }

}
