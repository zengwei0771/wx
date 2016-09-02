<?php
    require('config.php');
?>
<html>
    <head>
        <meta http-equiv="Cache-Control" content="no-transform" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>页面未找到--<?php echo $site_name;?></title>
        <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="bookmark" type="image/x-icon" /> 
        <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="icon" type="image/x-icon" /> 
        <link href="/static/favicon.ico" mce_href="/static/favicon.ico" rel="shortcut icon" type="image/x-icon" /> 
    </head>
    <body>
        <div style="margin:160px 0 0 0;text-align:center;color:#8f8f8f;font-size:20px;">
            页面未找到, <span id="second">3</span>秒后跳转到<a href="http://<?php echo $site_domain;?>/" title="<?php echo $site_name;?>" style="color:#62b934;text-decoration:none;">首页</a>
        </div>

        <script>
            setTimeout(function() {
                location.href = "http://<?php echo $site_domain;?>/";
            }, 3000);
            setInterval(function() {
                if (document.getElementById('second').innerHTML > 1) {
                    document.getElementById('second').innerHTML = document.getElementById('second').innerHTML - 1;
                }
            }, 1000);
        </script>
    </body>
</html>
