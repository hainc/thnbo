=== ThnBo ===
Contributors: 源分享
Donate link: https://www.yfx.top
Tags: thnbo
Requires at least: 3.5
Tested up to: 6.0.3
Requires PHP: 7.4
Stable tag: 1.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

ThnBo是一款针对WordPress开发的缩略图美化插件,为广大站长提供缩略图的美化便利

== Description ==

ThnBo是一款针对WordPress开发的缩略图美化插件,为广大站长提供缩略图的美化便利

== Installation ==

安装后启用即可，该插件自动对所有发布和更新操作生效

== Changelog ==

= 1.7 =
1.修复了可能与其他插件或项目发生的图片处理库冲突问题。
2.新增 腾讯云 COS 上传支持：
    ·图片在裁剪处理后将自动上传至 COS 并直接应用
    ·上传成功后会自动删除本地缓存文件
    ·WordPress 媒体库中将显示 COS 的直链地址
    ·注意：启用 COS 支持前需先安装并启用官方腾讯云 COS 插件
参考资源：
    ·插件说明文档：https://cloud.tencent.com/document/product/436/41153
    ·插件下载地址：https://cosbrowser.cloud.tencent.com/code/tencent-cloud-cos.zip

= 1.6 =
1.优化PHP版本兼容性，最低支持PHP 7.4
2.全面兼容PHP 8.x版本
3.添加PHP版本检查，不满足版本要求时显示警告
4.优化代码结构，提高稳定性
5.更新composer.json中的PHP版本要求

= 1.5 =
1.新增关闭设置项不显示相关内容
2.新增默认关闭设置不显示文章Mete Box
3.修复文章Mete Box选择关闭点击发布/更新文章报错
4.修复后台图片不显示问题
5.兼容PHP 7.4和PHP 8.x

= 1.4 =
1.优化默认美化缩略图为资源模板
2.新增批量更新文章特色图像功能
3.替换设置页已失效的展示内容信息
4.发布文章时与设置项新增关闭美化特色图像功能

= 1.3.1 =
1.修复批量删除缩略图功能不生效的问题

= 1.3.0 =
1.增加文章Meta Box，可对文章使用单独的缩略图主题
2.增加一键删除缩略图功能
3.增加插件列表页设置按钮
4.适配多站点模式

= 1.2.0 =
1.支持抓取设置了来源防盗链的图片

= 1.1.1 =
1.修复了可能与其他项目产生图像处理库冲突的问题

= 1.1.0 =
1.新增多款美化模板
2.新增多款裁剪方式
3.新增支持选择默认美化主题
4.修复已知问题.优化插件使用效率

= 1.0.2 =
1.修复自动保存文章多次生成缩略图的BUG

= 1.0.1 =
1.修复某些情况下会产生异常的问题

= 1.0.0 =
1.方便快捷节省时间
2.发布-更新即可完善想要的效果