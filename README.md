## DPlayer-WordPress: DPlayer for WordPress

### 2018-6-7更新

瞎几把改了注释，也不知道对不对；

给插件添加了插入按钮，可以直接填入常用参数；

给插入按钮添加了列表生成的iframe，调用的 https://github.com/menhood/DPlayer-list-iframe （性能巨差，但总归能用）；

太丑什么的就不要在意了😂

#### 下面用中文解释使用方法（英语渣已炸）
短代码调用：
直接在文本模式下输入以下代码即可调用
```
[dplayer url="https://ddns.menhood.wang:2233/Violet.mp4" pic="https://ddns.menhood.wang:2233/violet.jpg" danmu=true /]
```

`url`是视频地址；

`pic`是视频封面地址；

`danmu`是弹幕开关，true是打开，false是关闭；

还有一些其他的参数：

自动播放：'autoplay'、截图：'screenshot'、循环播放：'loop'、预加载：'preload'、热键：'hotkey'

flv和hls支持在wordpress仪表盘`设置-DPlayer`里面,token和弹幕服务器地址可以默认，也可以根据自己的需要修改，

这里是[弹幕服务器后端](https://github.com/menhood/DPlayer-node),

其他参数看起来好像没有定义，看的不是很懂，今天先到这。

### 原文档
[DPlayer](https://github.com/DIYgod/DPlayer) is such a lovely HTML5 danmaku video player by [DIYGod](https://github.com/DIYgod), and it's used on many platforms (as listed below). 
- [DPlayer-for-typecho](https://github.com/volio/DPlayer-for-typecho)

- [Hexo-tag-dplayer](https://github.com/NextMoe/hexo-tag-dplayer)

- [DPlayer_for_Z-BlogPHP](https://github.com/fghrsh/DPlayer_for_Z-BlogPHP)

- [纸飞机视频区插件(DPlayer for Discuz!)](https://coding.net/u/Click_04/p/video/git)

- [dplayer_py_backend](https://github.com/dixyes/dplayer_py_backend)

- [dplayer_lua_backend](https://github.com/dixyes/dplayer_lua_backend)

Today, DPlayer is coming to WordPress.

Usage is rather simple, and here is the template of shortcode we supported.
[dplayer url="https://anotherhome.net/DIYgod-cannot-even-discribe.mp4" pic="https://anotherhome.com/DIYgod-cannot-even-discribe.png" autoplay="true" danmu="true"/]

Parameter 'url' is the source URL to the video file, you can upload the video to your WordPress library, then use it here.
Parameter 'pic' is the poster of the video. And it's optional.
Parameter 'autoplay', as the name suggests, if it is true, then once the video is prepared, it starts to play . Default false and it is optional also.
Parameter 'screenshot', enable screenshot?. Optional and default false.
Parameter 'loop', enable loop?. Optional and default false.
Parameter 'preload', preload mode, 'auto', 'metatdata' or 'none'. Optional and default metadata.
Parameter 'hotkey', enable builtin hotkey? including left, right and Space. Optional and default true.
Parameter 'danmu', should DPlayer load danmaku. Default false and it's optional.

### Screenshots

1. Write the shortcode manually in your editor
![Screenshot 1](https://raw.githubusercontent.com/BlueCocoa/DPlayer-WordPress/master/assets/screenshot-1.png)

2. Save and you’ll get this lovely danmuku video player!
![Screenshot 2](https://raw.githubusercontent.com/BlueCocoa/DPlayer-WordPress/master/assets/screenshot-2.png)

3. Now we can edit danmaku API URL and token in settings page
![Screenshot 3](https://raw.githubusercontent.com/BlueCocoa/DPlayer-WordPress/master/assets/screenshot-3.png)
