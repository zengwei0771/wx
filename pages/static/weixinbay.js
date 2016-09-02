
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
