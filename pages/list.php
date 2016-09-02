<?php
    require_once('config.php');
    require_once('function.php');

    list($articles, $hasmore) = get_articles($_GET['catagory'], $_GET['sortby'], $_GET['page']);
    $rand_articles = get_rand_articles();
    $hot_accounts = get_hot_accounts();
    if ($_GET['catagory'] and $_GET['catagory'] != 'all') {
        $catagory_name = get_catagory_name_by_id($_GET['catagory']);
    }
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
    <script>
        window.onscroll = pagescroll('hot_read');
    </script>
</head>
<body>
    <div class="head_wrap">
        <?php include 'head.php';?>
    </div>
    <div class="body">
        <div class="leftbar">
            <ul><?php include 'leftnavi.php';?></ul>
            <div class="qrcode"><img src="/static/qrcode.png" width="160" /><a href="http://<?php echo $m_site_domain;?>/" title="<?php echo $site_name?>移动站">扫一扫 手机版</a></div>
        </div>
        <div id="rightbar" class="rightbar">
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
                    echo '<li><a title="'.$ha->account_name.'" href="/baccount/'.$ha->aid.'/"><span>'.$ha->account_name.'</span><span>'.$ha->allread.'</span><span>'.$ha->allagree.'</span></a></li>';
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
                            if ($_GET['prefix'] == 'hotread') {
                                echo '><a href="/hotread/" title="微信文章热门排行">热门排行</a>';
                            } else if ($_GET['prefix'] == 'recommend') {
                                echo '><a href="/recommend/" title="微信文章推荐阅读">推荐阅读</a>';
                            } else if ($_GET['prefix'] == 'hotagree') {
                                echo '><a href="/hotagree/" title="微信文章点赞热门">点赞热门</a>';
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
                    if ($a->read == 100000) {
                        $read = '100000+';
                    } else {
                        $read = $a->read;
                    }
                    echo '<article><a target="_blank" title="'.$a->title.'" href="/barticle/'.$a->titleid.'.html" /><script>window.img=\'<img id="img" src="'.$a->cover.'" height="160" width="196" />\';document.write("<iframe src=\'javascript:parent.img;\' height=\'160\' width=\'196\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");</script></a><h2><a target="_blank" href="/barticle/'.$a->titleid.'.html" title="'.$a->title.'">'.$a->title.'</a></h2><p>'.$a->article_desc.'</p><footer><time>'.explode(' ',$a->time)[0].'</time><a class="account" href="/baccount/'.$a->account_id.'/" title="微信公众号'.$a->account_name.'">@'.$a->account_name.'</a><span>阅读('.$read.')</span><a class="agree" href="javascript:void(0);"><img src="/static/muzhi.svg"/>'.$a->agree.'</a></footer></article>';
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
                            $html .= '/'.($_GET['page']+1).'/">下一页</a>';
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
