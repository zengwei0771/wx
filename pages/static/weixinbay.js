var JQ = jQuery;

function pagescroll(box) {
  return function() {
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
    var rightbarheight = 0;
    var children = document.getElementById('rightbar').children;
    for (var i = 0; i < children.length; i++) {
        rightbarheight += children[i].clientHeight + 40;
    }
    if( t >= rightbarheight) { 
        if (JQ('#'+box).css('position') == 'relative') {
            JQ('#'+box).css({'position': 'fixed', 'display': 'none'});
            JQ('#'+box).fadeIn();
            JQ('#ad1').css({'display': 'none'});
        }
        JQ('#gotop').fadeIn();
    } else { 
        if (JQ('#'+box).css('position') == 'fixed') {
          JQ('#'+box).css({'position': 'relative'});
          JQ('#ad1').css({'display': 'block'});
        }
        JQ('#gotop').fadeOut();
    } 
  } 
}

function goTop() {
    JQ('html,body').animate({scrollTop:0}, 500);
}

function video_load_next() {
    if (JQ('.next-page a').length > 0) {
        var href = JQ('.next-page a').attr('part-href');
        console.log(href);
        JQ('.next-page').html('加载中。。。');
        JQ.get(href, function(ret) {
            JQ('.next-page').replaceWith(ret);
        });
    }
}

JQ(function() {
    JQ('.search').bind('keypress',function(event){
        if(event.keyCode == "13") {
            var q = JQ('.search').val();
            document.location.href = '/?q=' + q;
        }
    });
});
