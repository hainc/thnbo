<?php
/**
 * 脚本注册文件
 *
 * @package THNBO
 */

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style( 'layui_css', THNBO_ROOT_URL . 'assets/layui/css/layui.css', array(), THNBO_VERSION );
    wp_enqueue_style( 'thnbo_css', THNBO_ROOT_URL . 'assets/css/thnbo.css', array( 'layui_css' ), THNBO_VERSION );

    wp_enqueue_script( 'layui_js', THNBO_ROOT_URL . 'assets/layui/layui.js', false, THNBO_VERSION );
    wp_enqueue_script( 'thnbo_start_js', THNBO_ROOT_URL . 'assets/layui/start.js', false, THNBO_VERSION, true );
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_script( 'thnbo_pro_js', THNBO_ROOT_URL . 'assets/js/thnbo-pro.js', false, THNBO_VERSION, true );

    wp_localize_script( 'thnbo_pro_js', 'thnbo_pro_api', array(
        'root' => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' )
    ) );
}, 9999);

add_action('admin_enqueue_scripts', function () {
    $js = <<<html
setTimeout("show_cut_theme()","1000");
function show_cut_theme() {
jQuery(function ($) {
  var i = $('#cut_theme .layui-anim-upbit dd.layui-this').index('#cut_theme .layui-anim-upbit dd');
  $(".layui-form-mid.layui-word-aux").find("li").eq(i).show().siblings().hide();
  $('#cut_theme .layui-form-select dd').on("click", function () {
    var i = $('#cut_theme .layui-anim-upbit dd.layui-this').index('#cut_theme .layui-anim-upbit dd');
    $(".layui-form-mid.layui-word-aux").find("li").eq(i).show().siblings().hide();
    if(i != 0){
       $("#other").show(); 
    }else{
       $("#other").hide();  
    }
    
  });
});
}
html;
    wp_add_inline_script( 'thnbo_start_js', $js );
}, 9999);