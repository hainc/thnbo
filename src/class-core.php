<?php
/**
 * 核心功能文件
 * 负责处理缩略图生成、裁剪和上传等核心功能
 */

namespace THNBO\Src;

use THNBO\Grafika\Grafika;

class Core {

    /**
     * 文件裁剪类型
     * 默认为顶部居中裁剪
     */
    private string $_cut_type = Cut_Type::TOP_CENTER;

    /**
     * 背景主题
     * 默认为文章主题
     */
    private string $_cut_theme = Cut_Theme::POST;

    /**
     * 设置裁剪类型
     *
     * @param string $cut_type 要设置的裁剪类型，从Cut_Type静态类读取
     */
    public function set_cut_type( string $cut_type ): void {
        $this->_cut_type = $cut_type;
    }

    /**
     * 设置背景主题
     *
     * @param string $cut_theme 要设置的背景主题，从Cut_Theme静态类读取
     */
    public function set_cut_theme( string $cut_theme ): void {
        $this->_cut_theme = $cut_theme;
    }

    /**
     * 注册WordPress钩子
     * 绑定缩略图处理逻辑到文章发布和编辑事件
     */
    public function register_hook(): void {
        // publish_post 钩子：文章发布时触发，仅接受1个参数 $post_id
        // edit_post 钩子：文章编辑时触发，接受2个参数 $post_id 和 $post
        add_action( 'publish_post', [ $this, 'handle_thumbnail' ], 2, 1 );
        add_action( 'edit_post', [ $this, 'handle_thumbnail' ], 2, 2 );
        
        // 注册后台元框
        add_action( 'admin_init', [ $this, 'add_metabox' ] );
    }

    /**
     * 添加后台元框
     * 在文章编辑页面添加缩略图模式选择框
     */
    public function add_metabox(): void {
        // 仅当主题未关闭时显示元框
        if ( thnbo_get_option( 'cut_theme' ) !== Cut_Theme::CLOSE ) {
            add_meta_box( 
                'cut_meta', 
                '缩略图模式', 
                function ( $post ) {
                    // 获取当前文章的缩略图主题设置，若无则使用全局设置
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
                }, 
                'post', 
                'side', 
                'high' 
            );
        }
    }
    
    /**
     * 处理缩略图生成和裁剪
     * 响应文章发布和编辑事件，生成带边框的缩略图
     *
     * @param int      $post_id 文章ID
     * @param WP_Post|null $post 文章对象，可选参数
     */
    public function handle_thumbnail( int $post_id, ?\WP_Post $post = null ): void {
        // 如果只有一个参数，获取文章对象
        if ( is_null( $post ) ) {
            $post = get_post( $post_id );
            // 无法获取文章对象时，终止执行
            if ( ! $post ) {
                return;
            }
        }
        
        // 处理删除缩略图请求
        $thnbo_type = $_GET['thnbo_type'] ?? '';
        if ( 'delete' === $thnbo_type ) {
            $this->_delete_thumbnail( $post_id );
            return;
        }
        
        // 仅当主题未关闭时执行
        if ( $this->_cut_theme !== Cut_Theme::CLOSE ) {
            // 获取现有的缩略图相关ID
            $cut_id = get_post_meta( $post_id, 'cut_id', true );
            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
            
            // 检查 _thumbnail_id 指向的文件是否存在
            if ( ! empty( $thumbnail_id ) ) {
                $thumbnail_file = get_attached_file( $thumbnail_id );
                if ( ! $thumbnail_file || ! file_exists( $thumbnail_file ) ) {
                    // 如果 _thumbnail_id 指向的文件不存在，删除该meta
                    delete_post_meta( $post_id, '_thumbnail_id' );
                    $thumbnail_id = '';
                }
            }
            
            // 检查 cut_id 指向的文件是否存在
            $cut_exists = false;
            if ( ! empty( $cut_id ) ) {
                $cut_file = get_attached_file( $cut_id );
                if ( $cut_file && file_exists( $cut_file ) ) {
                    $cut_exists = true;
                } else {
                    // 如果 cut_id 指向的文件不存在，删除对应的meta
                    delete_post_meta( $post_id, 'cut_id' );
                    delete_post_meta( $post_id, 'cut_theme' );
                    $cut_id = '';
                }
            }
            
            // 如果 cut_id 存在且文件有效，则继续使用该缩略图
            if ( $cut_exists ) {
                // 如果 cut_id 和 _thumbnail_id 不一致，将 _thumbnail_id 设置为 cut_id
                if ( $cut_id !== $thumbnail_id ) {
                    set_post_thumbnail( $post_id, $cut_id );
                }
                return;
            }
            
            // 如果 cut_id 不存在或文件无效，但 _thumbnail_id 存在且文件有效，则使用该图片并更新 cut_id
            if ( ! empty( $thumbnail_id ) ) {
                update_post_meta( $post_id, 'cut_id', $thumbnail_id );
                update_post_meta( $post_id, 'cut_theme', $this->_cut_theme );
                return;
            }
            
            // 生成新的缩略图
            $this->_generate_thumbnail( $post_id, $post );
        }
    }
    
    /**
     * 删除缩略图
     *
     * @param int $post_id 文章ID
     */
    private function _delete_thumbnail( int $post_id ): void {
        // 删除缩略图相关元数据
        delete_post_meta( $post_id, 'cut_id' );
        delete_post_meta( $post_id, 'cut_theme' );

        // 获取并删除缩略图附件
        $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
        if ( ! empty( $thumbnail_id ) ) {
            wp_delete_post( $thumbnail_id, true );
            delete_post_meta( $post_id, '_thumbnail_id' );
        }
    }
    
    /**
     * 生成缩略图
     *
     * @param int     $post_id 文章ID
     * @param \WP_Post $post 文章对象
     */
    private function _generate_thumbnail( int $post_id, \WP_Post $post ): void {
        // 获取现有缩略图信息
        $exist_cut_id       = get_post_meta( $post_id, 'cut_id', true );
        $exist_cut_theme    = get_post_meta( $post_id, 'cut_theme', true );
        $exist_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

        // 已启用主题时，终止执行
        if ( ! empty( $exist_cut_theme ) ) {
            return;
        }
        
        // 缩略图ID与cut_id一致时，终止执行
        if ( $exist_cut_id === $exist_thumbnail_id && ! empty( $exist_thumbnail_id ) ) {
            return;
        }
        
        // 仅处理post类型文章
        if ( $post->post_type !== 'post' ) {
            return;
        }
        
        // 仅处理发布、未来发布、草稿和待审核状态的文章
        if ( ! in_array( $post->post_status, [ 'publish', 'future', 'draft', 'pending' ] ) ) {
            return;
        }

        $super_out_path = '';

        // 文章无缩略图时，从内容中提取图片
        if ( ! has_post_thumbnail( $post_id ) ) {
            $super_out_path = $this->_extract_image_from_content( $post_id, $post );
            
            // 提取成功后，直接返回，因为图片已经处理完成
            if ( ! empty( $super_out_path ) ) {
                return;
            }
        } else {
            // 获取现有缩略图ID
            $exist_thumbnail_id = get_post_thumbnail_id( $post_id );
            
            // 获取完整的缩略图URL
            $full_image_url = wp_get_attachment_image_src( $exist_thumbnail_id, 'full' );
            
            // 无法获取缩略图URL时，终止执行
            if ( ! $full_image_url ) {
                return;
            }
            
            // 检查现有缩略图文件名是否已经包含 _thumbnail 后缀
            // 如果包含，则不再处理，避免重复生成
            $full_image_filename = pathinfo( $full_image_url[0], PATHINFO_FILENAME );
            if ( strpos( $full_image_filename, '_thumbnail' ) !== false ) {
                return;
            }
            
            // 生成处理后的缩略图
            $this->_process_thumbnail( $post_id, $full_image_url, $exist_thumbnail_id, $exist_cut_id, $super_out_path );
        }
    }
    
    /**
     * 从文章内容中提取图片
     *
     * @param int     $post_id 文章ID
     * @param \WP_Post $post 文章对象
     * @return string 生成的缩略图路径，失败时返回空字符串
     */
    private function _extract_image_from_content( int $post_id, \WP_Post $post ): string {
        // 从文章内容中匹配图片标签
        $post_content = get_the_content( null, null, $post );
        preg_match_all( '/<img[^>]*?src="([^"]*?)"[^>]*?>/i', $post_content, $match );
        
        // 无匹配图片时，返回空
        if ( ! array_key_exists( 1, $match ) || ! array_key_exists( 0, $match[1] ) || empty( $match[1][0] ) ) {
            return '';
        }
        
        $image_url = $match[1][0];
        
        // 生成临时文件名和路径
        $tmp_file_name = pathinfo( $image_url, PATHINFO_FILENAME ) . '_thumbnail.png';
        $tmp_file = THNBO_ROOT_PATH . 'tmp/' . $tmp_file_name;
        
        // 下载或复制图片到本地临时目录
        if ( ! $this->_download_or_copy_image( $image_url, $tmp_file ) ) {
            return '';
        }
        
        // 处理图片
        $theme_meta = Cut_Theme::get_theme_meta( $this->_cut_theme );
        $now_tpl = THNBO_ROOT_PATH . $theme_meta['tpls'][ mt_rand( 0, count( $theme_meta['tpls'] ) - 1 ) ];
        $editor = Grafika::createEditor();
        $out_path = wp_upload_dir()['path'] . '/' . $tmp_file_name;
        
        // 编辑图片
        $this->_edit_image( $editor, $tmp_file, $now_tpl, $out_path, $theme_meta );
        
        // 插入附件并设置为特色图像
        $exist_thumbnail_id = $this->_insert_attachment( $out_path, $post_id );
        if ( set_post_thumbnail( $post_id, $exist_thumbnail_id ) ) {
            // 清理临时文件
            $this->_cleanup_temp_files( $tmp_file );
            return $out_path;
        }
        
        // 清理临时文件
        $this->_cleanup_temp_files( $tmp_file );
        return '';
    }
    
    /**
     * 下载或复制图片
     * 根据是否启用COS，选择不同的图片获取方式
     *
     * @param string $image_url 图片URL
     * @param string $out_path 输出路径
     * @return bool 成功返回true，失败返回false
     */
    private function _download_or_copy_image( string $image_url, string $out_path ): bool {
        // 检查是否启用了COS
        $use_cos = thnbo_get_option( 'use_cos' );
        $cos_url_path = get_option( 'upload_url_path' );
        
        if ( $use_cos && ! empty( $cos_url_path ) ) {
            // COS已启用，从远程下载图片
            $response = wp_remote_get( $image_url, [
                'stream'    => true,
                'filename'  => $out_path,
                'timeout'   => 5000,
                'headers'   => [
                    'referer' => home_url( '/' ),
                ],
            ] );
            
            return ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200;
        } else {
            // COS未启用，尝试从本地复制图片
            $local_path = str_replace( home_url( '/' ), ABSPATH, $image_url );
            
            if ( file_exists( $local_path ) ) {
                // 本地文件存在，直接复制
                return copy( $local_path, $out_path ) !== false;
            } else {
                // 本地文件不存在，尝试从远程下载
                $response = wp_remote_get( $image_url, [
                    'stream'    => true,
                    'filename'  => $out_path,
                    'timeout'   => 5000,
                    'headers'   => [
                        'referer' => home_url( '/' ),
                    ],
                ] );
                
                return ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200;
            }
        }
    }
    
    /**
     * 处理缩略图
     * 包括裁剪、合成和上传等操作
     *
     * @param int    $post_id 文章ID
     * @param array  $full_image_url 完整的缩略图URL数组
     * @param int    $exist_thumbnail_id 现有缩略图ID
     * @param string $exist_cut_id 现有裁剪ID
     * @param string $super_out_path 超级输出路径
     */
    private function _process_thumbnail( int $post_id, array $full_image_url, int $exist_thumbnail_id, string $exist_cut_id, string $super_out_path ): void {
        // 获取主题元数据
        $theme_meta = Cut_Theme::get_theme_meta( $this->_cut_theme );
        $now_tpl = THNBO_ROOT_PATH . $theme_meta['tpls'][ mt_rand( 0, count( $theme_meta['tpls'] ) - 1 ) ];
        
        // 创建编辑器实例
        $editor = Grafika::createEditor();
        
        // 生成临时文件名和路径
        $full_image_filename = pathinfo( $full_image_url[0], PATHINFO_FILENAME );
        
        // 检查文件名是否已经包含 _thumbnail 后缀，如果包含，则不再添加
        if ( strpos( $full_image_filename, '_thumbnail' ) !== false ) {
            $tmp_file_name = $full_image_filename . '.png';
        } else {
            $tmp_file_name = $full_image_filename . '_thumbnail.png';
        }
        
        $tmp_file = THNBO_ROOT_PATH . 'tmp/' . $tmp_file_name;
        $out_path = wp_upload_dir()['path'] . '/' . $tmp_file_name;
        
        // 下载或复制完整图片到临时目录
        $this->_download_or_copy_image( $full_image_url[0], $tmp_file );
        
        // 处理图片（裁剪、合成等）
        $thnbo_type = $_GET['thnbo_type'] ?? '';
        if ( 'delete' !== $thnbo_type ) {
            $this->_edit_image( $editor, $tmp_file, $now_tpl, $out_path, $theme_meta );
        } else {
            // 删除操作时，直接复制文件
            copy( $tmp_file, $out_path );
        }
        
        // 删除旧的缩略图（如果需要）
        if ( ! empty( $super_out_path ) ) {
            wp_delete_attachment( $exist_thumbnail_id );
        }
        
        // 插入新的附件并设置为特色图像
        $attachment_id = $this->_insert_attachment( $out_path, $post_id );
        if ( set_post_thumbnail( $post_id, $attachment_id ) ) {
            $this->_update_thumbnail_metadata( $post_id, $attachment_id, $exist_cut_id );
        }
        
        // 清理临时文件
        $this->_cleanup_temp_files( $tmp_file );
    }
    
    /**
     * 编辑图片
     * 执行裁剪、合成等操作
     *
     * @param \THNBO\Grafika\EditorInterface $editor 编辑器实例
     * @param string $in_path 输入图片路径
     * @param string $tpl_path 模板路径
     * @param string $out_path 输出图片路径
     * @param array $theme_meta 主题元数据
     */
    private function _edit_image( \THNBO\Grafika\EditorInterface $editor, string $in_path, string $tpl_path, string $out_path, array $theme_meta ): void {
        // 声明并初始化图片对象
        $image1 = null;
        $image2 = null;
        
        // 打开输入图片
        $editor->open( $image1, $in_path );
        
        // 根据裁剪类型执行裁剪操作
        switch ( $this->_cut_type ) {
            case Cut_Type::SMART:
                $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'smart' );
                break;
            case Cut_Type::TOP_CENTER:
                $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'top-center' );
                break;
            case Cut_Type::CENTER:
                $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'center' );
                break;
            case Cut_Type::BOTTOM_CENTER:
                $editor->resizeFill( $image1, $theme_meta['width'], $theme_meta['height'], 'bottom-center' );
                break;
            default:
                $editor->resizeExact( $image1, $theme_meta['width'], $theme_meta['height'] );
        }
        
        // 保存裁剪后的图片到临时文件
        $process_tmp_file = THNBO_ROOT_PATH . 'tmp/tmp.png';
        $editor->save( $image1, $process_tmp_file );
        
        // 打开模板图片和裁剪后的图片，执行合成操作
        $editor->open( $image1, $tpl_path );
        $editor->open( $image2, $process_tmp_file );
        $editor->blend( $image1, $image2, 'normal', 1, $theme_meta['position'], $theme_meta['offset_x'], $theme_meta['offset_y'] );
        
        // 保存合成后的图片
        $editor->save( $image1, $out_path );
        
        // 删除处理过程中生成的临时文件
        if ( file_exists( $process_tmp_file ) ) {
            unlink( $process_tmp_file );
        }
    }
    
    /**
     * 更新缩略图元数据
     *
     * @param int $post_id 文章ID
     * @param int $attachment_id 附件ID
     * @param string $exist_cut_id 现有裁剪ID
     */
    private function _update_thumbnail_metadata( int $post_id, int $attachment_id, string $exist_cut_id ): void {
        // 删除旧的裁剪元数据
        if ( ! empty( $exist_cut_id ) ) {
            delete_post_meta( $post_id, 'cut_id' );
            delete_post_meta( $post_id, 'cut_theme' );
        }
        
        // 添加新的裁剪元数据
        $thnbo_type = $_GET['thnbo_type'] ?? '';
        if ( 'delete' !== $thnbo_type ) {
            add_post_meta( $post_id, 'cut_id', $attachment_id, true );
            update_post_meta( $post_id, 'cut_theme', $this->_cut_theme );
        }
    }
    
    /**
     * 清理临时文件
     *
     * @param string $tmp_file 临时文件路径
     */
    private function _cleanup_temp_files( string $tmp_file ): void {
        // 删除主临时文件
        if ( file_exists( $tmp_file ) ) {
            unlink( $tmp_file );
        }
        
        // 删除处理过程中生成的临时文件
        $process_tmp_file = THNBO_ROOT_PATH . 'tmp/tmp.png';
        if ( file_exists( $process_tmp_file ) ) {
            unlink( $process_tmp_file );
        }
    }
    
    /**
     * 插入附件
     *
     * @param string $file 附件的完整路径
     * @param int $id 对应的文章ID
     * @return int|false 成功返回附件ID，失败返回false
     */
    private function _insert_attachment( string $file, int $id ) {
        $dirs       = wp_upload_dir();
        $filetype   = wp_check_filetype( $file );
        
        // 检查是否使用COS
        $use_cos = thnbo_get_option( 'use_cos' );
        $cos_url_path = get_option( 'upload_url_path' );
        
        // 生成GUID（全局唯一标识符）
        if ( $use_cos && ! empty( $cos_url_path ) ) {
            // 使用COS URL作为GUID
            $guid = $cos_url_path . '/' . _wp_relative_upload_path( $file );
        } else {
            // 使用本地URL作为GUID
            $guid = $dirs['baseurl'] . '/' . _wp_relative_upload_path( $file );
        }
        
        // 准备附件数据
        $attachment = [
            'guid'           => $guid,
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        // 插入附件
        $attach_id = wp_insert_attachment( $attachment, $file, $id );
        if ( ! $attach_id ) {
            return false;
        }
        
        // 生成并更新附件元数据
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }

}