<?php
/**
 * 核心功能文件
 */

namespace THNBO\Src;

use THNBO\Grafika\Grafika;
use WP_Error;

class Core {

    /**
     * 文件裁剪类型
     */
    private string $_cut_type = Cut_Type::TOP_CENTER;

    /**
     * 背景主题
     */
    private string $_cut_theme = Cut_Theme::POST;

    /**
     * 设置裁剪类型
     *
     * @param string $cut_type 要设置的裁剪类型，从Cut_Theme静态类读取
     */
    public function set_cut_type( string $cut_type ) {
        $this->_cut_type = $cut_type;
    }

    /**
     * 设置背景主题
     *
     * @param string $cut_theme 要设置的背景主题，从Cut_Theme静态类读取
     */
    public function set_cut_theme( string $cut_theme ) {
        $this->_cut_theme = $cut_theme;
    }

    /**
     * 注册钩子
     */
    public function register_hook() {
        add_action( 'publish_post', array( $this, 'handle_thumbnail' ), 2, 100 );
        add_action( 'edit_post', array( $this, 'handle_thumbnail' ), 2, 100 );
        add_action( 'admin_init', array( $this, 'add_metabox' ) );
    }

    public function add_metabox() {
        if(thnbo_get_option( 'cut_theme' ) !== Cut_Theme::CLOSE){
            add_meta_box( 'cut_meta', '缩略图模式', function ( $post ) {
                $cut_theme = get_post_meta( $post->ID, 'cut_theme', true ) ?: thnbo_get_option( 'cut_theme' );
                echo '<p><input type="radio" name="cut_theme" value="' . Cut_Theme::CLOSE . '"/>
                关闭 <input type="radio" name="cut_theme" value="' . Cut_Theme::RESOURCE . '"';
                if ( $cut_theme === Cut_Theme::RESOURCE ) {
                    echo 'checked';
                }
                echo '/>资源 <input type="radio" name="cut_theme" value="' . Cut_Theme::MATERIAL . '"';
                if ( $cut_theme === Cut_Theme::MATERIAL ) {
                    echo 'checked';
                }
                echo '/>素材 <input type="radio" name="cut_theme" value="' . Cut_Theme::POST . '"';
                if ( $cut_theme === Cut_Theme::POST ) {
                    echo 'checked';
                }
                echo '/>文章 <input type="radio" name="cut_theme" value="' . Cut_Theme::BILL . '"';
                if ( $cut_theme === Cut_Theme::BILL ) {
                    echo 'checked';
                }
                echo '/>海报</p><p>注意：<br/>1、生成后无法修改，请谨慎选择；<br/>2、自动替换特色图像；<br/>3、自动保存到媒体库中。</p>';
            }, 'post', 'side', 'high' );
        }
    }
    

    public function handle_thumbnail( $post_id, $post ) {
        $thnbo_type = $_GET['thnbo_type'] ?? '';
        if ( 'delete' === $thnbo_type ) {
            delete_post_meta( $post_id, 'cut_id' );
            delete_post_meta( $post_id, 'cut_theme' );

            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
            if ( ! empty( $thumbnail_id ) ) {
                wp_delete_post( $thumbnail_id, true );

                delete_post_meta( $post_id, '_thumbnail_id' );
            }
        }
        if($this->_cut_theme !== Cut_Theme::CLOSE){
            
            $exist_cut_id       = get_post_meta( $post_id, 'cut_id', true );
            $exist_cut_theme    = get_post_meta( $post_id, 'cut_theme', true );
            $exist_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

            /** 如果没设置post_type就跳过 */
            if ( ! isset( $_POST['post_type'] ) && ! isset( $_GET['thnbo_type'] ) ) {
             return;
            }

            /** 更新时如果启用了主题则终止执行 */
            if ( ! empty( $exist_cut_theme ) ) {
             return;
            }
            /** 当文章状态不是发布则终止执行 */
            if ( $post->post_status !== 'publish' ) {
             return;
            }
            /** 当缩略图_thumbnail_id存在并且和cut_id一致时终止执行 */
            if ( $exist_cut_id === $exist_thumbnail_id && ! empty( $exist_thumbnail_id ) ) {
             return;
            }
            /** 如果当前文章类型不是post则终止执行 */
            if ( $post->post_type !== 'post' ) {
             return;
            }

            $super_out_path = '';

            if ( ! has_post_thumbnail( $post_id ) ) {
             $post_content = get_the_content( null, null, $post );
             preg_match_all( '/<img[^>]*?src="([^"]*?)"[^>]*?>/i', $post_content, $match );
             if ( ! array_key_exists( 1, $match ) || ! array_key_exists( 0, $match[1] ) || empty( $match[1][0] ) ) {
                 return;
             }
             $tmp_file_name = pathinfo( $match[1][0], PATHINFO_FILENAME ) . '_thumbnail.png';
             $out_path      = wp_upload_dir()['path'] . '/' . $tmp_file_name;
             if ( ! copy( $match[1][0], $out_path ) ) {
                 return;
             }
             $exist_thumbnail_id = $this->_insert_attachment( $out_path, $post_id );
             if ( set_post_thumbnail( $post_id, $exist_thumbnail_id ) ) {
                 $super_out_path = $out_path;
             }
            }

            $full_image_url = wp_get_attachment_image_src( $exist_thumbnail_id, 'full' );

            if ( $full_image_url ) {
                $theme_meta    = Cut_Theme::get_theme_meta( $this->_cut_theme );
                $now_tpl       = THNBO_ROOT_PATH . $theme_meta['tpls'][ mt_rand( 0, count( $theme_meta['tpls'] ) - 1 ) ];
                $editor        = Grafika::createEditor();
                $tmp_file_name = pathinfo( $full_image_url[0], PATHINFO_FILENAME ) . '_thumbnail.png';
                $tmp_file      = THNBO_ROOT_PATH . 'tmp/' . $tmp_file_name;
    
                wp_remote_get( $full_image_url[0], array(
                    'stream'	=> true,
                    'filename'	=> $tmp_file,
                    'timeout'   => 5000,
                    'headers'   => array(
                        'referer' => home_url( '/' ),
                    ),
                ) );
    
                $in       = $tmp_file;
                $out_path = wp_upload_dir()['path'] . '/' . $tmp_file_name;
                $thnbo_type = $_GET['thnbo_type'] ?? '';
                if ( 'delete' !== $thnbo_type ) {
                    $editor->open( $image1, $in );
                    if ( $this->_cut_type === Cut_Type::SMART ) {
                        $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'smart' );
                    } elseif ( $this->_cut_type === Cut_Type::TOP_CENTER ) {
                        $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'top-center' );
                    } elseif ( $this->_cut_type === Cut_Type::CENTER ) {
                        $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'center' );
                    } elseif ( $this->_cut_type === Cut_Type::BOTTOM_CENTER ) {
                        $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'bottom-center' );
                    } else {
                        $editor->resizeExact( $image1, $theme_meta['width'], $theme_meta['height'] );
                    }
                    $editor->save( $image1, THNBO_ROOT_PATH . 'tmp/tmp.png' );
                    $editor->open( $image1, $now_tpl );
                    $editor->open( $image2, THNBO_ROOT_PATH . 'tmp/tmp.png' );
                    $editor->blend( $image1, $image2, 'normal', 1, $theme_meta['position'], $theme_meta['offset_x'], $theme_meta['offset_y'] );
                    $editor->save( $image1, $out_path );
                } else {
                    copy( $tmp_file, $out_path );
                }
    
                if ( ! empty( $super_out_path ) ) {
                    wp_delete_attachment( $exist_thumbnail_id );
                }
                $attachment_id = $this->_insert_attachment( $out_path, $post_id );
                if ( set_post_thumbnail( $post_id, $attachment_id ) ) {
                    if ( ! empty( $exist_cut_id ) ) {
                        delete_post_meta( $post_id, 'cut_id' );
                        delete_post_meta( $post_id, 'cut_theme' );
                    }
                    if ( 'delete' !== @$_GET['thnbo_type'] ) {
                        add_post_meta( $post_id, 'cut_id', $attachment_id, true );
                        update_post_meta( $post_id, 'cut_theme', $this->_cut_theme );
                    }
                }
                
                // 删除临时文件
                if ( file_exists( $tmp_file ) ) {
                    unlink( $tmp_file );
                }
                
                // 删除处理过程中生成的临时文件
                $process_tmp_file = THNBO_ROOT_PATH . 'tmp/tmp.png';
                if ( file_exists( $process_tmp_file ) ) {
                    unlink( $process_tmp_file );
                }
            }
        }
    }

    /**
     * 插入附件
     *
     * @param $file string 附件的完整路径
     * @param $id int 对应的文章的ID
     *
     * @return int|WP_Error 成功返回附件ID失败返回WP_Error
     */
    private function _insert_attachment( string $file, int $id ) {
        $dirs       = wp_upload_dir();
        $filetype   = wp_check_filetype( $file );
        
        // 检查是否使用COS
        $use_cos = thnbo_get_option( 'use_cos' );
        $cos_url_path = get_option( 'upload_url_path' );
        
        // 如果使用COS且cos_url_path已设置，则使用COS URL作为guid
        if ( $use_cos && ! empty( $cos_url_path ) ) {
            $guid = $cos_url_path . '/' . _wp_relative_upload_path( $file );
        } else {
            $guid = $dirs['baseurl'] . '/' . _wp_relative_upload_path( $file );
        }
        
        $attachment = array(
            'guid'           => $guid,
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file, $id );
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            include_once( ABSPATH . DIRECTORY_SEPARATOR . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'image.php' );
        }
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

}