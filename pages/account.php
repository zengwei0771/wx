<?php
    require_once('config.php');
    require_once('function.php');

    list($account, $articles, $hasmore) = get_account($_GET['accountid'], $_GET['page']);
    $hot_accounts = get_hot_accounts();

    $catagorys = get_catagorys();
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>微信文章精选,<?php echo $account->name;?>文章,热门公众号文章,原创文章,热门微信文章排行榜--<?php echo $site_name;?></title>

    <meta name="keywords" content="微信文章精选,<?php echo $account->name;?>文章,热门公众号文章,原创文章,热门微信文章排行榜" />
    <meta name="description" content="微信公众号<?php echo $account->name;?>文章。<?php echo $account->desc;?>" />

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
                    <?php if($account->aid){?>
                    <img src="http://open.weixin.qq.com/qr/code/?username=<?php echo $account->aid;?>" height="100" width="100" />
                    <?php }?>
                    <div class="account-name">
                    <?php if($account->aid){?>
                        <a href="/baccount/<?php echo $account->aid;?>/" title="<?php echo $account->name;?>所有文章"><?php echo $account->name;?></a>
                    <?php }else{?>
                        <a href="" title="<?php echo $account->name;?>所有文章"><?php echo $account->name;?></a>
                    <?php }?>
                    </div>
                    <div class="account-id"><strong>微信号:</strong>&nbsp;<?php echo $account->aid;?></div>
                    <div class="account-desc"><strong>介&nbsp;绍:</strong>&nbsp;<?php echo $account->desc;?></div>
                </div>
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
                    >
                    <a href="/baccount/<?php echo $account->aid;?>/" title="公众号<?php echo $account->name;?>文章"><?php echo $account->name;?></a>
                </li>
            </ul>
            <div class="article">
                <?php
                foreach($articles as $a) {
                    $arr = explode('/', $a->cover);
                    if ($a->read == 100000) {
                        $read = '100000+';
                    } else {
                        $read = $a->read;
                    }
                    echo '<article><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->titleid.'.html" /><script>window.img=\'<img id="img" src="'.$a->cover.'" height="160" width="196" />\';document.write("<iframe src=\'javascript:parent.img;\' height=\'160\' width=\'196\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");</script></a><h2><a target="_blank" href="/barticle/'.$a->titleid.'.html" title="'.$a->title.'">'.$a->title.'</a></h2><p>'.$a->desc.'</p><footer><time>'.explode(' ',$a->time)[0].'</time><a class="account" href="/baccount/'.$account->aid.'/" title="微信公众号'.$account->name.'">@'.$account->name.'</a><span>阅读('.$read.')</span><a class="agree" href="javascript:void(0);"><img src="/static/muzhi.svg"/>'.$a->agree.'</a></footer></article>';
                }
                ?>
                <div class="next-page">
                    <?php
                        if ($hasmore) {
                            echo '<a href="/baccount/'.$account->aid.'/'.($_GET['page']+1).'/">下一页</a>';
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
