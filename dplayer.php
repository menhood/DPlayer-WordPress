<?php
/*
* Plugin Name: DPlayer for WordPress
* Description: Wow, such a lovely HTML5 danmaku video player comes to WordPress
* Version: 1.2.2
* Author: 0xBBC
* Author URI: https://blog.0xbbc.com/
* License: GPLv3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*
* Acknowledgement
*  DPlayer by DIYgod
*    https://github.com/MoePlayer/DPlayer
*
*  And part of this work is done under Copy and paste programming :)
*    Thanks to https://github.com/MoePlayer/DPlayer-Typecho
*/
//↓↓↓载入配置文件
require_once( dirname( __FILE__ ) . '/dplayer-admin.php' );
add_action('media_buttons', 'add_my_media_button',15);
function add_my_media_button() {
    wp_enqueue_script( 'custombackend', '/custom.js', array(), '1.0.0', true );
    echo <<<EOF
    <input type="button" id="ShowButton_2" name="ShowButton_2" value="显示DP具体参数" class="button">
    <style>
    #dplayerinput{display:none;margin:5px;width:400px;height:100%;}
    #iframechild{border-radius:10px;}
    h4{display:inline}
    </style>
<div id="" style="display: flex;" >
    <div id="dplayerinput"  >
    
    <br>
    <h4>图片地址格式：</h4>http://ddns.menhood.wang/img.jpg
    <br>
    <h4>视频地址格式</h4>：http://ddns.menhood.wang/video.mp4
    <br>
    <h4>图片地址：</h4><input type="text" oninput="dplayerurls()" value=""  width=100% id="dplayerpic">
    <br>
    <h4>视频地址：</h4><input type="text" oninput="dplayerurls()" value=""  width=100% id="dplayerurl">
    <br>
    <h4>是否开启弹幕:</h4><input type="checkbox" id="danmucheck" >
    <script>
    $(function(){
        $("#ShowButton_2").click(
            function(){
                 if($("#dplayerinput").css("display")=='none'){
                    $("#dplayerinput").slideDown();
                    
                    $("#ShowButton_2").val("隐藏DP具体参数");
                 }else{
                    $("#dplayerinput").slideUp();
                    $("#ShowButton_2").val("显示DP具体参数");
                 }
            });
        });
    $(function(){
        $("#dplisgenerate").click(
            function(){
                 if($("#dplistiframe").css("display")=='none'){
                    $("#dplistiframe").slideDown();
                    $("#dplisgenerate").val("隐藏列表生成页");
                 }else{
                    $("#dplistiframe").slideUp();
                    $("#dplisgenerate").val("显示列表生成页");
                 }
            });
        });
      

	    var dplayerurl;
	    var dplayerpic;
	    var danmucheack;

	    function dplayerurls(){
		   dplayerurl = document.getElementById("dplayerurl").value;
		   dplayerpic = document.getElementById("dplayerpic").value;
		   dpmax = document.getElementById("dpmax").value;
		   
	    }//文本框参数处理
	    
	    function dplayerinsert(){
	    if (document.getElementById("danmucheck").checked){
          danmucheack="true";
      }else {
        danmucheack="false";
      }//检查弹幕checkbox是否选中
        document.getElementById("content").value = '[dplayer url="' + dplayerurl + '" pic="' + dplayerpic + '" danmu='+danmucheack+' / ]';
        
      }//插入单个视频代码
      
      function dpclear(){
        document.getElementById("content").value = '';
      }//清除输入代码
      
</script>
    <br>
    
    <a href="javascript:void(0);" onclick="dplayerinsert()" class="button">插入视频</a>
    <input type="button" id="dplisgenerate" name="dplisgenerate" value="生成列表" class="button">
    <a href="javascript:void(0);" onclick="dpclear()" class="button">清除输入</a>
    
    </div>
    <div id="dplistiframe" style="display:none; ">
    <iframe name="iframechild" id="iframechild" src="http://api.menhood.wang/dpplayist/index.php"  border="0" frameborder="no" framespacing="0" allowfullscreen="true" width=768 height=350 > </iframe>
    </div>
 </div>    
EOF;

}
//↓↓↓模块DPlayer
class DPlayer {
    static $add_script;//定义静态变量：添加脚本
    
    public static function init() {
        register_activation_hook( __FILE__, array( __CLASS__, 'dplayer_install' ) );//注册钩子 安装dplayer
        register_deactivation_hook( __FILE__, array( __CLASS__, 'dplayer_uninstall' ) );//注册钩子 卸载dplayer
        add_action( 'wp_head', array( __CLASS__, 'ready') );//添加动作 到wp头部 
        add_action( 'wp_footer', array( __CLASS__, 'add_script' ) );//添加动作 到wp脚部
        add_shortcode( 'dplayer', array( __CLASS__, 'dplayer_load') );//添加短代码 dplayer加载
        add_filter( 'plugin_action_links', array( __CLASS__, 'dplayer_settings_link' ), 9, 2 );
        //添加挂载 add_filter参数：指定要添加过滤器函数名字，我们自定义的过滤器函数名字，指定过滤器的优先级，指定过滤器接收的参数个数 有返回值：True
    }//公共静态函数 载入
    
    public static function ready() {
?>
<script>var dPlayers = [];var dPlayerOptions = [];</script>
<?php
    }//公共静态函数 准备完成 js代码定义dPlayers为空数组，dPlayer选项为空数组
    
    //↓↓↓ registers default options 注册默认选项
    public static function dplayer_install() {
        add_option( 'kblog_danmaku_url', '//danmaku.daoapp.io' );//添加选项 弹幕服务器地址 默认值
        add_option( 'kblog_danmaku_token', 'tokendemo' );//添加选项 弹幕令牌 默认值
        add_option( 'kblog_danmaku_dplayer_version', '1.6.1' );//添加选项 dplayer版本 默认值
        add_option( 'kblog_danmaku_dplayer_version_check', '0' );//添加选项 dplayer版本检查 默认值
    }//公共静态函数 dplayer安装
    
    public static function dplayer_uninstall() {
        delete_option( 'kblog_danmaku_url' );//删除选项 弹幕服务器地址
        delete_option( 'kblog_danmaku_token' );//删除选项 弹幕令牌
        delete_option( 'kblog_danmaku_dplayer_version' );//删除选项 dplayer版本
        delete_option( 'kblog_danmaku_dplayer_version_check' );//删除选项 dplayer版本检查
    }//公共静态函数 dplayer卸载
    //↓↓↓公共静态函数dplayer B站链接处理……看不懂了，先放着
    public static function dplayer_bilibili_url_handler($bilibili_url) {
        $aid = 0;//定义变量 av号
        $page = 1;//定义变量 页码
        $is_bilibili = false;//定义变量 是否为B站链接 默认为 否
        if (preg_match('/^[\d]+$/', $bilibili_url)) {
            $aid = $bilibili_url;//av号为传入变量bilibili_url的值
            $is_bilibili = true;//是否为B站链接 布尔值为 是
        } else {
            $parsed = parse_url($bilibili_url);//解析B站url bilibili_url 赋值给变量parsed
            if ($parsed['host'] === 'www.bilibili.com') {
                preg_match('/^\/video\/av([\d]+)(?:\/index_([\d]+)\.html)?/', $parsed['path'], $path_match);//如果顶级域名为B站 正则匹配 获取路径
                if ($path_match) {
                    $is_bilibili = true;//是否为B站链接 布尔值为 是
                    $aid = $path_match[1];//av号获取路径数组第二个 即第一个符合正则被捕获到的文本
                    $page = $path_match[2] == null ? 1 : $path_match[2];//page变量获取到页码
                    preg_match('/^page=([\d]+)$/', $parsed['fragment'], $page_match);//正则匹配 搜索结果
                    if ($page_match) $page = $page_match[1];
                }
            }
        }//如果正则匹配符合bilibili_url：一整行1个以上的数字 则赋值；不匹配正则，则……

        if ($is_bilibili) {
            if ($page == 1) {
                $danmaku_url = stripslashes(get_option( 'kblog_danmaku_url', '' ));
                return array('danma' => $danmaku_url.'bilibili?aid='.$aid, 'video' => $danmaku_url.'/video/bilibili?aid='.$aid);
            } else {
                $cid = -1;
                $json_response = @json_decode(gzdecode(file_get_contents('http://www.bilibili.com/widget/getPageList?aid='.$aid)), true);
                if ($json_response) {
                    foreach ($json_response as $page_info) {
                        if ($page_info['page'] == $page) {
                            $cid = $page_info['cid'];
                            break;
                        }
                    }
                }
                
                if ($cid != -1) {
                    return array('danma' => $danmaku_url.'bilibili?cid='.$cid, 'video' => $danmaku_url.'/video/bilibili?cid='.$cid);
                }
            }
        }//如果判断为bilibili_url 页码为1 弹幕服务器地址为……
        return null;
    }
    //↓↓↓公共静态函数 dplayer加载
    public static function dplayer_load($atts, $content, $tag) {
        if ($atts == null) $atts = [];//如果传入的$atts为空 $atts为空数组
        if ($tag == null) $tag = '';//如果传入的$tag为空 $tag为空字符串
        
        // normalize attribute keys, lowercase 规范属性键 小写
        $atts = array_change_key_case((array)$atts, CASE_LOWER);//将$atts的内容转换为小写
        $bilibili_param = $atts['bilibili'] ? $atts['bilibili'] : '';//判断$atts内容是否为$atts['bilibili']，如果不是输出空字符串
        $id = md5($_SERVER['HTTP_HOST'] . $atts['url'] . $bilibili_param);//用域名+地址+bilibili计算哈希
        $result = array(
            'url' => $atts['url'] ? $atts['url'] : '',
            'pic' => $atts['pic'] ? $atts['pic'] : '',
            'type' => $atts['type'] ? $atts['type'] : 'auto', 
        );//结果数组取值 视频地址 图片地址 类型 
        
        $theme = $atts['theme'];//取变量atts中的theme数组赋值给变量theme
        if (!$theme) $theme = '#FADFA3';//如果theme变量为空 将字符串赋值给theme变量

        $data = array(
            'id' => $id,
            'autoplay' => false,
            'theme' => $theme,
            'loop' => false,
            'screenshot' => false,
            'hotkey' => true,
            'preload' => 'metadata'
        );// 定义了一个数组，包含一个数组成员：键名为id 值为false…… 定义数组id 自动播放 主题 循环 截图 热键 预加载

        if ($atts['autoplay']) $data['autoplay'] = ($atts['autoplay'] == 'true') ? true : false;//如果传入变量atts中的autoplay 为 true data变量赋值为true 否则为false 即默认为false
        if ($atts['loop']) $data['loop'] = ($atts['loop'] == 'true') ? true : false;//判断短代码loop的值，如果匹配true，则赋值给data变量为true 否则为false 即默认为false
        if ($atts['screenshot']) $data['screenshot'] = ($atts['screenshot'] == 'true') ? true : false;//同上
        if ($atts['hotkey']) $data['hotkey'] = ($atts['hotkey'] == 'true') ? true : false;//同上
        if ($atts['preload']) $data['preload'] = (in_array($atts['preload'], array('auto', 'metadata', 'none')) == true) ? $atts['preload'] : 'metadata';
        //搜索数组中是否存在以下值：auto, metadata, none 是则给data变量中的preload赋值atts的preload 否则给data变量中的preload赋值metadata
        

        $playerCode = '<div id="player'.$id.'" class="dplayer">';//播放器代码 开始标签 和 赋值给div的id 哈希计算的值
        $playerCode .= "</div>\n";//播放器代码 结束标签换行

        $danmaku = array(
            'id' => md5($id),
            'token' => get_option( 'kblog_danmaku_token', '' ),
            'api' => get_option( 'kblog_danmaku_url', '' ),
        );//弹幕数组定义 获取id token api地址
        
        if ($bilibili_param) {
            $bilibili_parsed = DPlayer::dplayer_bilibili_url_handler($bilibili_param);//引用方法处理参数
            $danmaku['addition'] = array($bilibili_parsed['danma']);//变量danmaku中的addition获取处理器处理后的数组中的danma
            if ($danmaku['addition'] && empty($atts['url'])) {
                $result['url'] = $bilibili_parsed['video'];//如果变量danmaku中的addition非空 并且 变量atts中的url值为空 变量result中的url值为处理之后的B站参数即变量$bilibili_parsed中的video
            }
        }//如果解析到B站参数 进行处理……具体没看懂……
        
        $data['video'] = $result;//data变量中的video获得变量result的值
        $data['danmaku'] = ($atts['danmu'] != 'false') ? $danmaku : null;//变量atts中的danmu为是情况下，data变量中的danmaku获得变量danmaku ；变量atts中的danmu为否情况下 ，data变量中的danmaku获得null

        $js = json_encode($data);//定义变量js为变量data进行json编码后的值
        $playerCode .= <<<EOF
<script>dPlayerOptions.push({$js});</script>
EOF;
        return $playerCode;
    }//播放器代码 插入播放器参数的js代码
    
    public static function dplayer_settings_link( $links, $file ) {
        if ( plugins_url('dplayer-admin.php', __FILE__) === $file && function_exists( 'admin_url' ) ) {
            $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=kblog-dplayer' ) ) . '">' . esc_html__( 'Settings' ) . '</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }//公共静态函数 dplayer设置链接 传入变量link和变量file 
    //如果 插件地址与变量file完全一直并且函数admin_url已被定义
    //变量$settings_link获得赋值 : 过滤 转义   管理URL的路径/options-general.php?page=kblog-dplayer  + Settings
    //向数组links插入新元素：变量settings_link
    //↓↓↓公共静态函数 添加脚本
    public static function add_script() {
        if (!self::$add_script) {
            if ( get_option( 'kblog_enable_flv' ) ) {
                wp_enqueue_script( '0-dplayer-flv', plugins_url('dplayer/plugin/flv.min.js', __FILE__), false, '1.4.0', false );
            }//判断flv
            if ( get_option( 'kblog_enable_hls' ) ) {
                wp_enqueue_script( '0-dplayer-hls', plugins_url('dplayer/plugin/hls.min.js', __FILE__), false, '1.4.0', false );
            }//判断hls
            
            $current_time = time();//定义变量当前时间获得赋值当前时间
            $last_check = (int)get_option( 'kblog_danmaku_dplayer_version_check', '0' );//定义变量last_check 获得赋值 获取参数整数值
            $dplayer_version = get_option( 'kblog_danmaku_dplayer_version', '1.6.1' );//定义变量dplayer_version 获得参数赋值
            
            if ($current_time - $last_check > 86400 /* 86400 = 60 * 60 * 24 i.e 24hrs */) {
                $response = wp_remote_get( 'https://cdnjs.cat.net/ajax/libs/dplayer/package.json' );
                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $body = $response['body']; // use the content
                    $json_data = @json_decode($body, true);
                    $json_dplayer_version = @$json_data['version'];
                    if (preg_grep('/^[\d\.]+$/', $json_dplayer_version)) {
                        if (strcmp($dplayer_version, $json_dplayer_version) != 0) {
                            update_option( 'kblog_danmaku_dplayer_version', $json_dplayer_version );
                            $dplayer_version = $json_dplayer_version;
                        }
                    }
                }
                update_option( 'kblog_danmaku_dplayer_version_check', $current_time );
            }//判断时间 获取参数 插入body 正则筛选 播放器版本判断
            
            wp_enqueue_style( 'dplayer', esc_url("https://cdn.bootcss.com/dplayer/$dplayer_version/DPlayer.min.css"), false, $dplayer_version, false );//加载播放器样式
            wp_enqueue_script( 'dplayer', esc_url("https://cdn.bootcss.com/dplayer/$dplayer_version/DPlayer.min.js"), false, $dplayer_version, false );//加载播放器脚本
            
            wp_enqueue_script( 'init-dplayer', plugins_url('dplayer/init-dplayer.js', __FILE__), false, '1.0.0', false );//加载播放器参数js代码
            /*wp_enqueue_script( 'init-dplayer-list', plugins_url('dplayer/init-dplayer-list.js', __FILE__), false, '1.0.0', false );//加载播放器参数js代码*/
            self::$add_script = true;//设置本函数的变量值为true
        } 
    }
};

DPlayer::init();//调用函数DPlayer


