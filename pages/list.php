<?php
    require_once('config.php');
    require_once('db.php');                                                 

    $sql = 'select * from article order by time desc limit 0, 40';
    $articles = $db->getObjListBySql($sql);

    $sql = 'select _type, count(*) as c from article group by _type order by c desc';
    $catagorys = $db->getObjListBySql($sql);

    $sql = 'select * from article order by `read` desc limit 0, 15';
    $hot_reads = $db->getObjListBySql($sql);

    $sql = 'select account, sum(`read`) as allread, sum(agree) as allagree from article group by account order by allagree desc limit 0, 15';
    $hot_accounts = $db->getObjListBySql($sql);
?>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>微信文章精选,热门公众号文章,原创文章,热门微信文章排行榜--<?php echo $site_name;?></title>

    <meta name="keywords" content="微信文章精选,热门公众号文章,原创文章,热门微信文章排行榜" />
    <meta name="description" content="" />

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
                <a href="http://<?php echo $site_domain;?>/" title="<?php echo $site_name;?>">
                    <img src="/static/longlogo.png" height="90" width="180" alt="<?php echo $site_name;?>"></img>
                </a>
            </div>
            <div class="right">
                <ul class="navi">
                <?php
                foreach(array_reverse(array_slice($catagorys, 0, 10)) as $c) {
                    echo '<li><a href="/catagory/'.$c->_type.'/" title="'.$c->_type.'">'.$c->_type.'</a><img src="/static/navi-split.png"/></li>';
                }
                ?>
                <li></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="body">
        <div class="leftbar">
            <ul>
                <li><a href="/hotread/" title="微信文章热门排行">热门排行</a></li>
                <li><a href="/recommend/" title="推荐文章阅读">推荐阅读</a></li>
                <li><a href="/hotagree/" title="微信文章点赞排行榜">点赞热门</a></li>
                <li><a href="/lastest/" title="最新收录文章">最新发布</a></li>
            </ul>
            <div class="qrcode"><img src="/static/qrcode.png" width="160" /><a href="http://<?php echo $m_site_domain;?>/" title="<?php echo $site_name?>移动站">扫一扫 手机版</a></div>
        </div>
        <div id="rightbar" class="rightbar">
            <div id="hot_read" class="box">
                <h3>热门阅读</h3>
                <ul>
                <?php
                foreach($hot_reads as $hr) {
                    echo '<li><a title="'.$hr->title.'" href="/barticle/'.$hr->title.'.html">'.$hr->title.'</a></li>';
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
                    echo '<li><a title="'.$ha->account.'" href="/account/'.$hr->account.'/"><span>'.$ha->account.'</span><span>'.$ha->allread.'</span><span>'.$ha->allagree.'</span></a></li>';
                }
                ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <ul class="address">
                <li>当前位置: <a href="/">首页</a></li>
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
                    echo '<article><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->title.'.html" /><script>window.img'.$a->_id.'=\'<img id="img'.$a->_id.'" src="'.$a->cover.'" height="144" width="180" />\';document.write("<iframe id=\'iframe'.$a->_id.'\' src=\'javascript:parent.img'.$a->_id.';\' height=\'160\' width=\'196\' frameBorder=\'0\' scrolling=\'no\'></iframe>");</script></a><h2><a href="/barticle/'.$a->title.'.html">'.$a->title.'</a></h2><p>'.$a->desc.'</p><footer><time>'.explode(' ',$a->time)[0].'</time><a class="account" href="/account/'.$a->account.'/">@'.$a->account.'</a><span>阅读('.$read.')</span><a class="agree" href="javascript:void(0);"><img src="/static/muzhi.svg"/>'.$a->agree.'</a></footer></article>';
                }
                ?>
                <div class="next-page">
                    <a href="/all/2/">下一页</a>
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
