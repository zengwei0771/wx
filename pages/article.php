<?php
    require_once('config.php');
    require_once('function.php');

    $article = get_article($_GET['titleid']);
    $account_articles  = get_account_articles($article->account_id);
    $more_articles = get_more_articles();

    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?php echo $article->title;?>--<?php echo $site_name;?></title>

    <meta name="keywords" content="<?php echo $article->account_name.','.$article->aid.','.$article->title;?>" />
    <meta name="description" content="<?php echo $article->title.','.$article->account_name.';'.$article->article_desc;?>" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script src="/static/weixinbay.js" language="JavaScript"></script>
    <script>
        window.onscroll = pagescroll('account_detail');
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
            <div id="account_detail" class="box">
                <h3>公众号信息</h3>
                <div>
                    <?php if($article->aid){?>
                    <img src="http://open.weixin.qq.com/qr/code/?username=<?php echo $article->aid;?>" height="100" width="100" />
                    <?php }?>
                    <div class="account-name">
                    <?php if($article->aid){?>
                        <a href="/baccount/<?php echo $article->aid;?>/" title="<?php echo $article->account_name;?>所有文章"><?php echo $article->account_name;?></a>
                    <?php }else{?>
                        <a href="" title="<?php echo $article->account_name;?>所有文章"><?php echo $article->account_name;?></a>
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
                <article >
                    <?php
                        echo $article->content;
                    ?>
                </article>
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
