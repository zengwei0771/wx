"use strict";
var system = require('system');
var webpage = require('webpage');

var url = 'http://weixin.sogou.com/weixin?type=1&query=%E7%94%9F%E6%B4%BB%E5%B0%8F%E5%B8%B8%E8%AF%86&ie=utf8&_sug_=n&_sug_type_=';
var page = webpage.create();

function get_article(eles, max) {
        console.log('2');
    if (max === 0) {
        phantom.exit();
    }
        console.log('3');

    var ele = eles[max-1];
        console.log('4');
    var p = webpage.create();
    console.log(ele.url);
    p.open(ele.url, function(s) {
        if (s != "success") {
            console.log('error on loading ' + ele.url);
        }

        p.injectJs('./jquery-2.0.3.min.js');
        var content = p.evaluate(function() {
            return $('#js_content').html();
        });
        console.log(content);

        p.close();
        get_article(eles, max-1);
    });
}

page.open(url, function(s) {
    if (s != "success") {
        console.log('error on loading ' + url);
        phantom.exit();
    }

    page.injectJs('./jquery-2.0.3.min.js');
    var number_url = page.evaluate(function() {
        return $('.wx-rb._item').attr('href');
    });

    page.open(number_url, function(s1) {
        if (s1 != "success") {
            console.log('error on loading ' + number_url);
            phantom.exit();
        }

        page.injectJs('./jquery-2.0.3.min.js');
        
        var hs = page.evaluate(function() {
            var hs = [];
            var i;
            for (i = 0; i < $('.weui_media_title').length; i++) {
                hs.push({
                    'text':$($('.weui_media_title')[i]).text().trim(),
                    'url':'http://mp.weixin.qq.com' + $($('.weui_media_title')[i]).attr('hrefs').trim()
                });
            }
            return hs;
        });
        console.log('1');
        page.close();
        get_article(hs, hs.length);
    });
});
