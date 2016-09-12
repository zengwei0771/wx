<?php
    require_once('../pages/config.php');
    require_once('../pages/function.php');

    list($videos, $hasmore) = get_videos($_GET['page']);

    $videos_html = '';
    foreach($videos as $v) {
        $src = str_replace('auto=1', 'auto=0', $v->video);
        if (!strpos($src, 'auto=0')) {
            if ($src[count($src)-1] != '&') {
                $src .= '&';
            }
            $src .= 'auto=0';
        }
       $videos_html .= '<div class="video"><a target="_blank" title="'.$v->title.'" href="/barticle/'.$v->titleid.'.html">'.$v->title.'</a><iframe allowfullscreen="" vidtype="1" frameborder="0" src="'.$src.'" style="z-index:1;"></iframe></div>';
    }
    $videos_html .= '<div class="next-page">';
    if ($hasmore) {
        $videos_html .= '<a href="/videos/'.($_GET['page']+1).'/" part-href="/videos/'.($_GET['page']+1).'/part/" onclick="video_load_next();return false;">下一页</a>';
    } else {
        $videos_html .= '没有更多内容';
    }
    $videos_html .= '</div>';

    if (isset($_GET['part']) && $_GET['part'] == '1') {
        echo $videos_html;
        exit(0);
    }

    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>

    <title>微信视频阅读,微信视频合辑,热门微信视频,微信视频文章,最火微信视频--<?php echo $site_name;?></title>

    <meta name="keywords" content="微信文章视频阅读,微信视频合辑,热门微信视频,微信视频文章,最火微信视频" />
    <meta name="description" content="最新的微信视频合辑，最全最火的视频热点，还有最新奇的微信视频，尽在<?php echo $site_name;?>。最全的微信文章获取渠道，大量的热门公众号的收录，超全的视频、图片链接分析，全在<?php echo $site_name;?>。" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script src="/static/weixinbay.js" language="JavaScript"></script>
    <script>
    $(function() {
        $('iframe').height($('iframe').width() * 0.75);
    });
    </script>
</head>
<body>
    <div class="head_wrap">
        <div class="head">
            <div class="left">
                <a href="javascript:void(0);" onclick="menu();" title="菜单"><img src="/static/menu.png" height="36" width="30" /></a>
            </div>
            <div class="right">
            <h2>视频聚合</h2>
            </div>
        </div>
    </div>
    <?php include 'menu.php';?>
    <div class="body">
        <div class="content">
            <div class="video-wrap">
                <?php echo $videos_html;?>
            </div>
        </div>
    </div>
    <div class="foot">
        <?php include 'foot.php';?>
    </div>
</body>
</html>
