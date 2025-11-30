<?php
/**
 * 设置页
 */

use THNBO\Src\Cut_Theme;
use THNBO\Src\Cut_Type;

add_filter( sprintf( '%splugin_action_links_%s', is_multisite() ? 'network_admin_' : '', BASENAME ), function ( $links ) {
    return array_merge(
        array(
            sprintf( '<a href="%s">%s</a>',
                network_admin_url( is_multisite() ? 'settings.php?page=thnbo' : 'options-general.php?page=thnbo' ),
                '设置' )
        ),
        $links
    );
} );

add_action( 'admin_menu', function () {
    add_options_page( 'ThnBo缩略图美化', 'ThnBo缩略图美化', 'manage_options', 'thnbo', function () {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( '权限不足' );
        }

        $thnbo_options = thnbo_get_option();

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $thnbo_options['cut_type'] = sanitize_text_field( $_POST['cut_type'] );
            $thnbo_options['cut_theme'] = sanitize_text_field( $_POST['cut_theme'] );
            update_option( 'thnbo_options', $thnbo_options );

            echo '<div class="notice notice-success settings-error is-dismissible"><p><strong>设置已保存。</strong></p></div>';
        }
        ?>
      <div class="container-laobuluo-main">
        <div class="laobuluo-wbs-header" style="margin-bottom: 15px;">
          <div class="laobuluo-wbs-logo">
            <a>
              <img src="<?php echo THNBO_ROOT_URL ?>assets/images/logo.png">
            </a><span class="wbs-span">ThnBo缩略图美化</span><span class="wbs-free">Free V<?php echo THNBO_VERSION; ?></span>
          </div>
          <div class="laobuluo-wbs-btn">
            <a class="layui-btn layui-btn-primary" href="https://www.yfx.top/" target="_blank">
              <i class="layui-icon layui-icon-home"></i> 插件主页
            </a>
            <a class="layui-btn layui-btn-primary" href="https://www.yfx.top/" target="_blank">
              <i class="layui-icon layui-icon-release"></i> 插件教程
            </a>
          </div>
        </div>
      </div>
      <!-- 内容 -->
      <div class="container-laobuluo-main">
        <div class="layui-container container-m">
          <div class="layui-row layui-col-space15">
            <!-- 左边 -->
            <div class="layui-col-md9">
              <div class="laobuluo-panel">
                <div class="laobuluo-controw">
                  <fieldset class="layui-elem-field layui-field-title site-title">
                    <legend>
                      <a>设置选项</a>
                    </legend>
                  </fieldset>
                  <form class="layui-form wpcosform" name="thnboform" method="post">
                    <div class="layui-form-item">
                      <label class="layui-form-label">默认主题</label>
                      <div class="layui-input-block" id="cut_theme">
                        <label>
                          <select name="cut_theme" onchange="change_cut_theme()">
                            <option value="<?php echo Cut_Theme::CLOSE ?>" <?php selected( $thnbo_options['cut_theme'], Cut_Theme::CLOSE ) ?>>关闭
                            </option>
                            <option value="<?php echo Cut_Theme::RESOURCE ?>" <?php selected( $thnbo_options['cut_theme'], Cut_Theme::RESOURCE ) ?>>资源
                            </option>
                            <option value="<?php echo Cut_Theme::MATERIAL ?>" <?php selected( $thnbo_options['cut_theme'], Cut_Theme::MATERIAL ) ?>>素材
                            </option>
                            <option value="<?php echo Cut_Theme::POST ?>" <?php selected( $thnbo_options['cut_theme'], Cut_Theme::POST ) ?>>文章
                            </option>
                            <option value="<?php echo Cut_Theme::BILL ?>" <?php selected( $thnbo_options['cut_theme'], Cut_Theme::BILL ) ?>>海报
                            </option>
                          </select>
                        </label>
                      </div>
                      <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                          <li id="tpl_0" <?php echo $thnbo_options['cut_theme'] !== Cut_Theme::CLOSE ? 'style="display: none;"' : ''; ?>>
                          </li>
                          <li id="tpl_1" <?php echo $thnbo_options['cut_theme'] !== Cut_Theme::RESOURCE ? 'style="display: none;"' : ''; ?>>
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/1/tpl_1.png" alt="资源主题">
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/1/tpl_2.png" alt="资源主题">
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/1/tpl_3.png" alt="资源主题">
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/1/tpl_4.png" alt="资源主题">
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/1/tpl_5.png" alt="资源主题">
                          </li>
                          <li id="tpl_2" <?php echo $thnbo_options['cut_theme'] !== Cut_Theme::MATERIAL ? 'style="display: none;"' : ''; ?>>
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/2/tpl_1.png" alt="素材主题">
                          </li>
                          <li id="tpl_3" <?php echo $thnbo_options['cut_theme'] !== Cut_Theme::POST ? 'style="display: none;"' : ''; ?>>
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/3/tpl_1.png" alt="文章主题">
                          </li>
                          <li id="tpl_4" <?php echo $thnbo_options['cut_theme'] !== Cut_Theme::BILL ? 'style="display: none;"' : ''; ?>>
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/4/tpl_1.png" alt="海报主题">
                            <img height="100" src="<?php echo THNBO_ROOT_URL ?>tpl/4/tpl_2.png" alt="海报主题">
                          </li>
                        </div>
                      </div>
                    </div>
                    <div class="layui-form-item" id="other" <?php echo $thnbo_options['cut_theme'] === Cut_Theme::CLOSE ? 'style="display: none;"' : ''; ?>>
                        <div class="layui-form-item">
                      <label class="layui-form-label">图像裁剪</label>
                      <div class="layui-input-block">
                        <select name="cut_type">
                          <option value="<?php echo Cut_Type::CENTER ?>" <?php selected( $thnbo_options['cut_type'], Cut_Type::CENTER ) ?>>居中裁剪
                          </option>
                          <option value="<?php echo Cut_Type::RESIZE_EXACT ?>" <?php selected( $thnbo_options['cut_type'], Cut_Type::RESIZE_EXACT ) ?>>强制缩放
                          </option>
                          <option value="<?php echo Cut_Type::SMART ?>" <?php selected( $thnbo_options['cut_type'], Cut_Type::SMART ) ?>>智能裁剪
                          </option>
                          <option value="<?php echo Cut_Type::TOP_CENTER ?>" <?php selected( $thnbo_options['cut_type'], Cut_Type::TOP_CENTER ) ?>>顶部居中裁剪
                          </option>
                        </select>
                      </div>
                      <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux">
                          如果图片过大会按照你选择的方式进行裁剪
                        </div>
                      </div>
                    </div>
                        <div class="layui-form-item">
                        <label class="layui-form-label">批量生成</label>
                        <div class="layui-input-block">
                          <button type="button" class="layui-btn layui-btn-primary thnbo-create">执行</button>
                          <div class="layui-progress progress-create" lay-filter="create" style="top: 16px; display: none;">
                            <div class="layui-progress-bar progress-bar-create" lay-percent="0%" style="width: 0%"></div>
                          </div>
                        </div>
                        <div class="layui-input-block">
                          <div class="layui-form-mid layui-word-aux desc-create">
                            批量为所有文章创建带边框的缩略图
                          </div>
                        </div>
                        </div>
                        <div class="layui-form-item">
                      <label class="layui-form-label">批量删除</label>
                      <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-primary thnbo-delete">执行</button>
                        <div class="layui-progress progress-delete" lay-filter="delete" style="top: 16px; display: none;">
                          <div class="layui-progress-bar progress-bar-delete" lay-percent="0%" style="width: 0%"></div>
                        </div>
                      </div>
                      <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux desc-delete">
                          一次性删除所有文章的缩略图边框
                        </div>
                      </div>
                    </div>
                    </div>
                      <?php
                      /**
                       * 为设置表单添加新字段
                       */
                      do_action( 'thnbo_settings_field' );
                      ?>
                    <div class="layui-form-item">
                      <label class="layui-form-label"></label>
                      <div class="layui-input-block"><input type="submit" name="submit" value="保存设置" class=" layui-btn"
                                                            lay-submit lay-filter="formDemo"/></div>
                    </div>
                    <input type="hidden" name="type" value="cos_info_set">
                  </form>
                </div>
              </div>
            </div>
            <!-- 左边 -->
            <!-- 右边 -->
            <div class="layui-col-md3">
              <div class="laobuluo-panel">
                <div class="laobuluo-panel-title">
                  商家推荐 <span class="layui-badge layui-bg-orange">官方唯一运营站点</span>
                </div>
                <div class="laobuluo-shangjia">
                  <a href="https://www.yfx.top" target="_blank">
                    <img src="<?php echo THNBO_ROOT_URL ?>assets/images/yfx.png">
                  </a>
                </div>
              </div>
              <div class="laobuluo-panel">
                <div class="laobuluo-panel-title">
                  扫码加QQ群
                </div>
                <div class="laobuluo-code">
                  <img src="<?php echo THNBO_ROOT_URL ?>assets/images/qrcode.jpg">
                  <p>
                    扫码加入 <span class="layui-badge layui-bg-blue">站长交流</span> QQ群
                  </p>
                  <p>
                    <span class="layui-badge">优先</span> 获取插件更新 和 更多 <span
                            class="layui-badge layui-bg-green">免费插件</span>
                  </p>
                </div>
              </div>
            </div>
            <!-- 右边 -->
          </div>
        </div>
      </div>
      <!-- 内容 -->
        <?php
    } );
} );
