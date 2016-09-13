<?php
    require_once('../pages/config.php');
    require_once('../pages/function.php');

    list($videos, $hasmore) = get_videos_with_account($_GET['page']);

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
</head>
<body>
    <div class="head_wrap">
        <?php include 'head.php';?>
    </div>
    <div class="body">
        <div class="content">
            <div class="article">
                <?php
                foreach($videos as $a) {
                    $arr = explode('/', $a->cover);
                    echo '<article><h2><a target="_blank" href="/barticle/'.$a->titleid.'.html" title="'.$a->title.'">'.$a->title.'</a></h2><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->titleid.'.html" /><script>window.img'.$a->titleid.'=\'<img id="img'.$a->titleid.'" src="'.$a->cover.'" height="90" width="120" />\';document.write("<iframe src=\'javascript:parent.img'.$a->titleid.';\' height=\'90\' width=\'120\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");</script></a><p>'.$a->article_desc.'</p><footer><a class="account" href="';
                    if ($a->account_id) {
                        echo '/baccount/'.$a->account_id.'/';
                    }
                    echo '" title="微信公众号'.$a->account_name.'">@'.$a->account_name.'</a><time>'.$a->date.'</time></footer></article>';
                }
                ?>
                <div class="next-page">
                    <?php
                        if ($hasmore) {
                            $html = '<a href="';
                            if ($_GET['prefix'] != 'none') {
                                if ($_GET['catagory'] != 'all') {
                                    $html .= '/catagory';
                                }
                                $html .= '/'.$_GET['prefix'];
                            }
                            $html .= '/'.($_GET['page']+1).'/';
                            if (isset($_GET['q'])) {
                                $html .= '?q='.$_GET['q'];
                            }
                            $html .= '">下一页</a>';
                            echo $html;
                        } else {
                            echo '没有更多内容';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="foot">
        <?php include 'foot.php';?>
    </div>
</body>
</html>
