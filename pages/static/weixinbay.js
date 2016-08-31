
function showImg(url) {
    var frameid = 'frameimg' + Math.random();
    window.img = '<img id="img" src=\''+url+'?'+Math.random()+'\' />';//<script>window.onload = function() { parent.document.getElementById(\''+frameid+'\').height = document.getElementById(\'img\').height+\'px\'; }<'+'/script>';
    document.write('<iframe id="'+frameid+'" src="javascript:parent.img;" frameBorder="0" scrolling="no" width="100%"></iframe>');
}

window.onscroll = function(){ 
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
    var hr = document.getElementById( "hot_read" ); 
    var rightbarheight = 0;
    var children = document.getElementById('rightbar').children;
    for (var i = 0; i < children.length; i++) {
        rightbarheight += children[i].clientHeight + 40;
    }
    if( t >= rightbarheight) { 
        if ($('#hot_read').css('position') == 'relative') {
            $('#hot_read').css({'position': 'fixed', 'display': 'none'});
            $('#hot_read').fadeIn();
        }
        $('#gotop').fadeIn();
    } else { 
        $('#hot_read').css({'position': 'relative'});
        $('#gotop').fadeOut();
    } 
} 

function goTop() {
    $('html,body').animate({scrollTop:0}, 500);
}
