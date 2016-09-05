<?php
    require_once('config.php');
    require_once('function.php');

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
       $videos_html .= '<div class="video"><a target="_blank" title="'.$v->title.'" href="/barticle/'.$v->titleid.'.html">'.$v->title.'</a><iframe allowfullscreen="" vidtype="1" frameborder="0" height="480" width="640" src="'.$src.'" style="z-index:1;"></iframe></div>';
    }
    $videos_html .= '<div class="next-page">';
    if ($hasmore) {
        $videos_html .= '<a href="/videos/'.($_GET['page']+1).'/" part-href="/videos/'.($_GET['page']+1).'/part/">下一页</a>';
    } else {
        $videos_html .= '没有更多内容';
    }
    $videos_html .= '</div>';

    if ($_GET['part'] == '1') {
        echo $videos_html;
        exit(0);
    }

    $rand_articles = get_rand_articles();
    $other_videos = get_other_videos();
    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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
        window.onscroll = pagescroll('other_video');
        $(function () {
            $(window).scroll(function () {
                if (200 + $(window).scrollTop() >= ($(document).height() - $(window).height())) {
                    video_load_next();
                }
            });
        })
    </script>
</head>
<body>
    <div class="head_wrap">
        <?php include 'head.php';?>
    </div>
    <div class="body">
        <div class="leftbar">
            <?php include 'leftnavi.php';?>
        </div>
        <div id="rightbar" class="rightbar">
            <div id="other_video" class="box">
                <h3>推荐视频</h3>
                <ul>
                <?php
                foreach($other_videos as $ov) {
                    echo '<li><a target="_blank" title="'.$ov->title.'" href="/barticle/'.$ov->titleid.'.html">'.$ov->title.'</a></li>';
                }
                ?>
                </ul>
            </div>
            <div id="hot_read" class="box">
                <h3>推荐热点</h3>
                <ul>
                <?php
                foreach($rand_articles as $ra) {
                    echo '<li><a target="_blank" title="'.$ra->title.'" href="/barticle/'.$ra->titleid.'.html">'.$ra->title.'</a></li>';
                }
                ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <ul class="address">
                <li>当前位置: 
                    <a href="/">首页</a>
                    ><a href="/videos/" title="微信视频合辑">视频</a>
                </li>
            </ul>
            <div class="video-wrap">
                <?php echo $videos_html;?>
            </div>
        </div>
        <div id="gotop">
            <a href="javascript:void(0);" title="回到顶部" onclick="goTop();"><img src="/static/gotop.gif" height="30" width="30" /></a>
        </div>
    </div>
    <div class="foot">
        <div class="txt">
            <ul class="info">
                <li class="sitename"><a href="http://<?php echo $site_domain;?>/"><?php echo $site_name;?>&copy;<?php echo date('Y');?></a></li>
                <li><a href="http://www.miitbeian.gov.cn/" target="_blank"><?php echo $beian;?></a></li>
                <li><script src="" language="JavaScript"></script></li>
            </ul>
            <div class="state">
                <?php echo $state;?>
                <p>联系人：<a href="mailto:<?php echo $mail;?>"><?php echo $mail;?></a></p>
            </div>
        </div>
    </div>
</body>
</html>
