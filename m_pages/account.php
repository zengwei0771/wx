<?php
    require_once('../pages/config.php');
    require_once('../pages/function.php');

    list($account, $articles, $hasmore) = get_account($_GET['accountid'], $_GET['page']);

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

    <title>微信文章精选,<?php echo $account->name;?>文章,热门公众号文章,原创文章,热门微信文章排行榜--<?php echo $site_name;?></title>

    <meta name="keywords" content="微信文章精选,<?php echo $account->name;?>文章,热门公众号文章,原创文章,热门微信文章排行榜" />
    <meta name="description" content="微信公众号<?php echo $account->name;?>文章。<?php echo $account->desc;?>" />

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
            <h2><?php echo $account->name;?></h2>
            <div id="account_detail">
                <?php if($account->aid){?>
                <img src="http://open.weixin.qq.com/qr/code/?username=<?php echo $account->aid;?>" height="128" width="128" />
                <?php }?>
                <div class="account-name">
                    <span class="account-id"><strong>微信号:</strong>&nbsp;<?php echo $account->aid;?></span>
                </div>
                <div class="account-desc"><strong>介&nbsp;绍:</strong>&nbsp;<?php echo $account->desc;?></div>
            </div>
            <h3>文章列表</h3>
            <ul id="other_article">
            <?php
            foreach($articles as $aa) {
                echo '<li><span>'.$aa->date.'&nbsp;&nbsp;</span><a target="_blank" title="'.$aa->title.'" href="/barticle/'.$aa->titleid.'.html">'.$aa->title.'</a></li>';
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
