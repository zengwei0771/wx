<?php
    require_once('config.php');
    require_once('function.php');

    $article = get_article($_GET['titleid']);
    $account_articles  = get_account_articles($article->account_id);
    $more_articles = get_more_articles($article->articleid);

    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?php echo $article->title;?>--<?php echo $site_name;?></title>

    <meta name="keywords" content="<?php echo $article->name.','.$article->aid.','.$article->title;?><?php if ($article->keywords) {echo ','.$article->keywords;}?>" />
    <meta name="description" content="<?php echo $article->title.','.$article->name.';'.$article->article_desc;?>" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script src="/static/weixinbay.js" language="JavaScript"></script>
    <script>
        var JQ = jQuery;
        window.onscroll = pagescroll('ad1');
        JQ(function() {
            setTimeout(function() {
                JQ.post('/daction/read/<?php echo $article->titleid;?>');
            }, 3000);
        });
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
            <div id="ad1" style="margin-bottom:20px;">
                <script type="text/javascript">
                    var sogou_ad_id=698655;
                    var sogou_ad_height=300;
                    var sogou_ad_width=360;
                </script>
                <script type='text/javascript' src='http://images.sohu.com/cs/jsfile/js/c.js'></script>
            </div>
            <div id="account_detail" class="box">
                <h3>公众号信息</h3>
                <div>
                    <?php if($article->aid){?>
                    <img src="http://open.weixin.qq.com/qr/code/?username=<?php echo $article->aid;?>" height="100" width="100" />
                    <?php }?>
                    <div class="account-name">
                    <?php if($article->aid){?>
                        <a href="/baccount/<?php echo $article->aid;?>/" title="<?php echo $article->name;?>所有文章"><?php echo $article->name;?></a>
                    <?php }else{?>
                        <a href="" title="<?php echo $article->name;?>所有文章"><?php echo $article->name;?></a>
                    <?php }?>
                    </div>
                    <div class="account-id"><strong>微信号:</strong>&nbsp;<?php echo $article->aid;?></div>
                    <div class="account-desc"><strong>介&nbsp;绍:</strong>&nbsp;<?php echo $article->account_desc;?></div>
                </div>
                <h3>其他文章</h3>
                <ul>
                <?php
                foreach($account_articles as $aa) {
                    echo '<li><a target="_blank" title="'.$aa->title.'" href="/barticle/'.$aa->titleid.'.html">'.$aa->title.'</a></li>';
                }
                ?>
                </ul>
            </div>
            <div id="" class="box">
                <h3>随机推荐</h3>
                <ul>
                <?php
                foreach($more_articles as $ma) {
                    echo '<li><a target="_blank" title="'.$ma->title.'" href="/barticle/'.$ma->titleid.'.html">'.$ma->title.'</a></li>';
                }
                ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <ul class="address">
                <li>当前位置: 
                    <a href="/">首页</a>
                    >
                    <a href="/catagory/<?php echo $article->catagoryid;?>/" title="<?php echo $article->catagory;?>"><?php echo $article->catagory;?></a>
                    >
                    <a href="/barticle/<?php echo $article->titleid;?>.html" title="<?php echo $article->title;?>"><?php echo $article->title;?></a>
                </li>
            </ul>
            <div id="article_content">
                <h2><?php echo $article->title;?></h2>
                <span><?php echo $article->date;?></span>
                <article >
                    <?php
                        include 'contents/'.$article->date.'/'.$article->titleid.'.html';
                    ?>
                </article>
                <?php
                    if ($article->vote) {
                        echo '<div id="cyPk" role="cylabs" data-use="pk" data-pkId="'.$article->vote.'" ></div>';
                    }
                ?>
                <div class="comment">
                    <!--高速版-->
                    <div id="SOHUCS" sid="article-<?php echo $article->articleid?>"></div>
                    <script charset="utf-8" type="text/javascript" src="http://changyan.sohu.com/upload/changyan.js" ></script>
                    <script type="text/javascript">
                    window.changyan.api.config({
                        appid: 'cysAptWrK',
                        conf: 'prod_57d72e31d2bd9f9a9b76f5f5ffe57ad4'
                    });
                    </script>
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
    <script type="text/javascript" charset="utf-8" src="https://changyan.sohu.com/js/changyan.labs.https.js?appid=cysAptWrK"></script>
</body>
</html>
