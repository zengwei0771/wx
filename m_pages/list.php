<?php
    require_once('../pages/config.php');
    require_once('../pages/function.php');

    if (isset($_GET['q'])) {
        list($articles, $hasmore) = search_articles($_GET['q'], $_GET['page']);
    } else {
        list($articles, $hasmore) = get_articles($_GET['catagory'], $_GET['sortby'], $_GET['page'], 20);
    }
    if ($_GET['catagory'] and $_GET['catagory'] != 'all') {
        $catagory_name = get_catagory_name_by_id($_GET['catagory']);
    }

    if ($_GET['catagory'] and $_GET['catagory'] != 'all') {
        $keywords = '微信文章精选,'.$catagory_name.','.$catagory_name.'微信文章'.','.$catagory_name.'原创文章,微信热文精选,微信文摘,微信文章大全,微信公众平台,微信文章,微信文章怎么写,微信文章哪里找,微信公众平台,微信营销,伤感文章,微信段子';
    } else {
        $keywords = '微信文章精选,热门公众号文章,原创文章,热门微信文章排行榜,微信热文精选,微信文摘,微信文章大全,微信公众平台,微信文章,微信文章怎么写,微信文章哪里找,微信公众平台,微信营销,伤感文章,微信段子';
    }

    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=640,initial-scale=1.0, minimum-scale=0.1, maximum-scale=10, user-scalable=yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>

    <title>微信文章精选,热门公众号文章,原创文章,微信段子,微信视频--<?php echo $site_name;?></title>

    <meta name="keywords" content="<?php echo $keywords;?>" />
    <meta name="description" content="最全的微信文章，最新的微信段子，最火的微信视频，最牛的微信营销案例，最美的微信深度美文，最具情怀的微信伤感文章，最具观点的微信原创文章。聚合内容涉及精选<?php foreach($catagorys as $c) {echo ','.$c->catagory;}?>等。" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script src="/static/weixinbay.js" language="JavaScript"></script>
</head>
<body>
    <div class="head_wrap">
        <div class="head">
            <div class="left">
                <a href="javascript:void(0);" onclick="menu();" title="菜单"><img src="/static/menu.png" height="36" width="30" /></a>
            </div>
            <div class="right">
                <h2>
                <?php
                    if ($_GET['catagory'] == 'all') {
                        if ($_GET['prefix'] == 'recommend') {
                            echo '推荐阅读';
                        } else if ($_GET['prefix'] == 'hotagree') {
                            echo '点赞热门';
                        } else if (isset($_GET['q'])) {
                            echo '搜索"'.$_GET['q'].'"';
                        } else {
                            echo '热门排行';
                        }
                    } else if ($_GET['catagory']) {
                        echo $catagory_name;
                    }
                ?>
                </h2>
            </div>
        </div>
    </div>
    <?php include 'menu.php';?>
    <div class="body">
        <div class="content">
            <div class="article">
                <?php
                foreach($articles as $a) {
                    $arr = explode('/', $a->cover);
                    echo '<article><h2><a target="_blank" href="/barticle/'.$a->titleid.'.html" title="'.$a->title.'">'.$a->title.'</a></h2><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->titleid.'.html" /><script>window.img'.$a->titleid.'=\'<img id="img'.$a->titleid.'" src="'.$a->cover.'" height="120" width="160" />\';document.write("<iframe src=\'javascript:parent.img'.$a->titleid.';\' height=\'120\' width=\'160\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");</script></a><p>'.$a->article_desc.'</p><footer><a class="account" href="';
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
