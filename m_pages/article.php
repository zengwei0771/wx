<?php
    require_once('../pages/config.php');
    require_once('../pages/function.php');

    $article = get_article($_GET['titleid']);
    $account_articles  = get_account_articles($article->account_id);

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

    <title><?php echo $article->title;?>--<?php echo $site_name;?></title>

    <meta name="keywords" content="<?php echo $article->account_name.','.$article->aid.','.$article->title;?>" />
    <meta name="description" content="<?php echo $article->title.','.$article->account_name.';'.$article->article_desc;?>" />

    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
    <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 

    <link rel="stylesheet" type="text/css" href="/static/weixinbay.css" media="all" />

    <script src="/static/jquery-3.1.0.min.js" language="JavaScript"></script>
    <script>
        var JQ = jQuery;
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
        <div class="content">
            <h2><?php echo $article->title;?></h2>
            <div class="author">
                <?php if($article->aid){?>
                    <a href="/baccount/<?php echo $article->aid;?>/" title="<?php echo $article->account_name;?>所有文章">@<?php echo $article->account_name;?></a>
                <?php }else{?>
                    <a href="" title="<?php echo $article->account_name;?>所有文章">@<?php echo $article->account_name;?></a>
                <?php }?>
                &nbsp;&nbsp;
                <span>发布于:<?php echo $article->date;?></span>
            </div>
            <div id="article_content">
                <article >
                    <?php
                        include '../pages/contents/'.$article->date.'/'.$article->titleid.'.html';
                    ?>
                </article>
            </div>
            <h3>其他文章</h3>
            <ul id="other_article">
            <?php
            foreach($account_articles as $aa) {
                echo '<li><a target="_blank" title="'.$aa->title.'" href="/barticle/'.$aa->titleid.'.html">'.$aa->title.'</a></li>';
            }
            ?>
            </ul>
        </div>
    </div>
    <div class="foot">
        <?php include 'foot.php';?>
    </div>
</body>
</html>
