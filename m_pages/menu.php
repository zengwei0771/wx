<div id="background" onmousewheel="return false;" onclick="background();return false;"></div>
<div id="menu" onmousewheel="return scroll(event,this)">
    <h5>推荐</h5>
    <ul>
        <li><a href="/hotread/" title="微信湾热门排行">热门排行</a></li>
        <li><a href="/hotagree/" title="微信湾点赞热门">点赞热门</a></li>
        <li><a href="/recommend/" title="微信湾推荐阅读">推荐阅读</a></li>
        <li><a href="/videos/" title="微信湾视频聚合">视频聚合</a></li>
        <li class="split"></li>
    </ul>
    <h5>分类</h5>
    <ul>
        <?php
        foreach($catagorys as $c) {
            echo '<li><a href="/catagory/'.$c->catagoryid.'/" title="'.$c->catagory.'"';
            if (isset($_GET['catagory']) && $c->catagoryid == $_GET['catagory']) {
                echo ' class="active"';
            }
            echo '>'.$c->catagory.'</a></li>';
        }
        ?>
    </ul>
</div>
