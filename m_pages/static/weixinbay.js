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
        }
        JQ('#gotop').fadeIn();
    } else { 
        JQ('#'+box).css({'position': 'relative'});
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

var scroll = function(event,scroller){
    var k = event.wheelDelta? event.wheelDelta:-event.detail*10;
    scroller.scrollTop = scroller.scrollTop - k;
    return false;
};

var menu = function() {
    if (JQ('#menu').attr('data-show') == '1') {
        return;
    }
    JQ('#background').show();
    JQ('#menu').animate({
        'left': '0px',
    }, 500);
    JQ('#menu').attr('data-show', '1');
}
var search = function() {
}
var background = function() {
    if (JQ('#menu').attr('data-show') == '1') {
        JQ('#menu').animate({
            'left': '-'+JQ('#menu').attr('data-width')+'px'
        }, 500);
        JQ('#menu').attr('data-show', '0');
    }
    JQ('#background').hide();
}

JQ(function() {
    var w = JQ('#menu').width();
    if (w == 160) {
        JQ('#menu').css('left', '-182px');
        JQ('#menu').attr('data-width', '182');
    } else {
        JQ('#menu').attr('data-width', w);
    }
    JQ('#menu').attr('data-show', '0');
});
