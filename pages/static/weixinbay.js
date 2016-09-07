
function pagescroll(box) {
  return function() {
    var t = document.documentElement.scrollTop || document.body.scrollTop;  
    var rightbarheight = 0;
    var children = document.getElementById('rightbar').children;
    for (var i = 0; i < children.length; i++) {
        rightbarheight += children[i].clientHeight + 40;
    }
    if( t >= rightbarheight) { 
        if ($('#'+box).css('position') == 'relative') {
            $('#'+box).css({'position': 'fixed', 'display': 'none'});
            $('#'+box).fadeIn();
        }
        $('#gotop').fadeIn();
    } else { 
        $('#'+box).css({'position': 'relative'});
        $('#gotop').fadeOut();
    } 
  } 
}

function goTop() {
    $('html,body').animate({scrollTop:0}, 500);
}

function video_load_next() {
    if ($('.next-page a').length > 0) {
        var href = $('.next-page a').attr('part-href');
        console.log(href);
        $('.next-page').html('加载中。。。');
        $.get(href, function(ret) {
            $('.next-page').replaceWith(ret);
        });
    }
}

$(function() {
    $('.search').bind('keypress',function(event){
        if(event.keyCode == "13") {
            var q = $('.search').val();
            document.location.href = '/?q=' + q;
        }
    });
});
