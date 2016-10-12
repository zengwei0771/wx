<?php
    require_once('config.php');
    require_once('function.php');

    if (isset($_GET['q'])) {
        list($articles, $hasmore) = search_articles($_GET['q'], $_GET['page']);
    } else {
        list($articles, $hasmore) = get_articles($_GET['catagory'], $_GET['sortby'], $_GET['page']);
    }
    $rand_articles = get_hot_articles();
    $hot_accounts = get_hot_accounts();
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
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>微信文章精选,热门公众号文章,原创文章,微信段子,微信视频--<?php echo $site_name;?></title>

    <meta name="keywords" content="<?php echo $keywords;?>" />
    <meta name="description" content="最全的微信文章，最新的微信段子，最火的微信视频，最牛的微信营销案例，最美的微信深度美文，最具情怀的微信伤感文章，最具观点的微信原创文章。聚合内容涉及精选<?php foreach($catagorys as $c) {echo ','.$c->catagory;}?>等。" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script src="/static/weixinbay.js" language="JavaScript"></script>
    <script>
        window.onscroll = pagescroll('hot_read');
        function agree(self) {
            if ($(self).attr('hasagree') == undefined) {
                $(self).attr('hasagree', 'hasagree');
                $.post('/daction/agree/' + $(self).attr('titleid'), function(ret){
                    $(self).removeAttr('hasagree');
                });
                $(self).children('span').text(Number($(self).children('span').text())+1);
            }
        }
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
            <div class="ad" style="position:fixed;margin-bottom:20px;">
                <script type="text/javascript">
                    var sogou_ad_id=698655;
                    var sogou_ad_height=200;
                    var sogou_ad_width=360;
                </script>
                <script type='text/javascript' src='http://images.sohu.com/cs/jsfile/js/c.js'></script>
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
            <div class="box">
                <h3>热门公众号</h3>
                <label class="hot-account-title"><span>公众号</span><span>阅读</span><span>点赞</span></label>
                <ul class="hot-account-list">
                <?php
                foreach($hot_accounts as $ha) {
                    echo '<li><a title="'.$ha->account_name.'" href="';
                    if ($ha->aid) {
                        echo '/baccount/'.$ha->aid.'/';
                    }
                    echo '"><span>'.$ha->account_name.'</span><span>'.$ha->allread.'</span><span>'.$ha->allagree.'</span></a></li>';
                }
                ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <ul class="address">
                <li>当前位置: 
                    <a href="/">首页</a>
                    <?php
                        if ($_GET['catagory'] == 'all') {
                            if ($_GET['prefix'] == 'newest') {
                                echo '><a href="/newest/" title="最新发布文章阅读">最新发布</a>';
                            } else if ($_GET['prefix'] == 'hotread') {
                                echo '><a href="/hotread/" title="微信文章热门排行">热门排行</a>';
                            } else if ($_GET['prefix'] == 'recommend') {
                                echo '><a href="/recommend/" title="微信文章推荐阅读">推荐阅读</a>';
                            } else if ($_GET['prefix'] == 'hotagree') {
                                echo '><a href="/hotagree/" title="微信文章点赞热门">点赞热门</a>';
                            } else if (isset($_GET['q'])) {
                                echo '><a href="/?q='.$_GET['q'].'" title="微信文章点赞热门">搜索"'.$_GET['q'].'"</a>';
                            }
                        } else if ($_GET['catagory']) {
                            echo '><a href="/catagory/'.$_GET['catagory'].'/" title="微信文章'.$catagory_name.'分类">'.$catagory_name.'</a>';
                        }
                    ?>
                </li>
            </ul>
            <div class="article">
                <?php
                foreach($articles as $a) {
                    $arr = explode('/', $a->cover);
                    echo '<article><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->titleid.'.html" /><script>window.img'.$a->titleid.'=\'<img id="img'.$a->titleid.'" src="'.$a->cover.'" height="160" width="196" />\';document.write("<iframe src=\'javascript:parent.img'.$a->titleid.';\' height=\'160\' width=\'196\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");</script></a><h2><a target="_blank" href="/barticle/'.$a->titleid.'.html" title="'.$a->title.'">'.$a->title.'</a></h2><p>'.$a->article_desc.'</p><footer><time>'.$a->date.'</time><a class="account" href="';
                    if ($a->account_id) {
                        echo '/baccount/'.$a->account_id.'/';
                    }
                    echo '" title="微信公众号'.$a->name.'">@'.$a->name.'</a><span>阅读('.$a->read.')</span><a class="agree" href="javascript:void(0);" onclick="agree(this)" titleid="'.$a->titleid.'"><img src="/static/muzhi.svg"/><span>'.$a->agree.'</span></a></footer></article>';
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
        <div id="gotop">
            <a href="javascript:void(0);" title="回到顶部" onclick="goTop();"><img src="/static/gotop.gif" height="30" width="30" /></a>
        </div>
    </div>
    <div class="foot">
        <?php include 'foot.php';?>
    </div>
</body>
</html>
