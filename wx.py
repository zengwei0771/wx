#!/usr/bin/env python
#encoding=utf8


import requests
import sys
sys.setrecursionlimit(10000)
from bs4 import BeautifulSoup
from common import UA, TO
import re


class WX():
    
    @staticmethod
    def article_info(url):
        try:
            c = requests.get(url, headers={'User-Agent':UA}, timeout=TO)
            cdom = BeautifulSoup(c.content, "html5lib", from_encoding="UTF-8")
            contentnode = cdom.find('div', id='js_content')
            if not contentnode:
                return None

            account_name = cdom.find('strong', class_='profile_nickname').text.strip()
            profile_meta_values = cdom.findAll('span', class_='profile_meta_value')
            account_id = profile_meta_values[0].text.strip()
            account_desc = profile_meta_values[1].text.strip()

            cover = None
            for line in c.content.split('\n'):
                line = line.strip()
                if line.startswith('var msg_cdn_url = "'):
                    cover = line[19:len(line)-2]
                    break
            if not cover:
                imgs = sorted(contentnode.findAll('img'), key=lambda x:int(x.get('data-w')), reverse=True)
                cover = imgs[0].get('data-src') if len(imgs) > 0 else ''

            desc = '\n'.join([i.strip() for i in contentnode.text.strip().split('\n') if i.strip()][0:4])

            content, videos, imgs = WX.content_handle(cdom, contentnode)
            return {
                'account_name': WX.filter_emoji(account_name),
                'account_id': account_id,
                'account_desc': WX.filter_emoji(account_desc),
                'cover': cover,
                'content': WX.filter_emoji(content),
                'desc': WX.filter_emoji(desc[:400]),
                'videos': videos,
                'imgs': imgs
            }
        except Exception, e:
            print e
            return None

    @staticmethod
    def content_handle(root, node):
        imgs = []
        img_id = 1;
        for img in node.findAll('img'):
            new_node = root.new_tag('div', **{'class':"img_wrap"})
            script_node = root.new_tag('script')
            new_node.append(script_node)
            src = img.get('data-src') if img.get('data-src') else (img.get('src') if img.get('src') else (img.get('data-data-src') if img.get('data-data-src') else ''))
            try:
                if img.get('width') and img['width'] != 'auto':
                    if img['width'].endswith('px'):
                        width = int(img['width'][:len(img['width'])-2])
                    else:
                        width = int(img['width'])
                else:
                    width = int(img.get('data-w'))
                img_width = width if width < 640 else 640
                img_height = int(img_width * float(img.get('data-ratio')) if img.get('data-ratio') else 0.4)
                if src and img_width > 200 and img_height > 200:
                    imgs.append({'src':src, 'width':img_width, 'height':img_height})
            except Exception, e:
                img_width = 400
                img_height = 240
            script_node.append('window.imgc'+str(img_id)+'=\'<img id="imgc'+str(img_id)+'" src="'+src+'" width="'+str(img_width)+'" height="'+str(img_height)+'" />\';document.write("<iframe src=\'javascript:parent.imgc'+str(img_id)+'\' width=\''+str(img_width)+'\' height=\''+str(img_height)+'\' frameBorder=\'0\' scrolling=\'no\' marginwidth=\'0\' marginheight=\'0\'></iframe>");')
            img_id += 1
            if img.get('style'):
                new_node['style'] = img['style']
            img.replace_with(new_node)
        for a in node.findAll('a'):
            a['rel'] = 'nofollow'
        videos = []
        for iframe in node.findAll('iframe'):
            src = (iframe.get('src') if iframe.get('src') else (iframe.get('data-src') if iframe.get('data-src') else (iframe.get('data-data-src') if iframe.get('data-data-src') else ''))).strip()
            if not src:
                print 'iframe has no src', str(iframe)
                iframe.decompose()
                continue

            if src.startswith('https://v.qq.com/') or src.startswith('http://v.qq.com/'):
                iframe['src'] = src
                iframe['width'] = '640'
                iframe['height'] = '480'
                videos.append(src)
            else:
                print 'iframe is not video', str(iframe)
                iframe.decompose()
                continue

            if not iframe.get('vidtype') and iframe.get('data-vidtype'):
                iframe['vidtype'] = iframe['data-vidtype']
        content = re.sub('background-image: ?url\(.+?\);?', '', str(node))
        content = re.sub('background=".+?"', '', content)
        return content, videos, imgs

    @staticmethod
    def filter_emoji(desstr, restr=''):  
        ''''' 
        过滤表情 
        '''  
        desstr = desstr.decode('utf8')
        try:  
            co = re.compile(u'[\U00010000-\U0010ffff]')  
        except re.error:  
            co = re.compile(u'[\uD800-\uDBFF][\uDC00-\uDFFF]')  
        return co.sub(restr, desstr)


if __name__ == '__main__':
    WX.article_info('http://mp.weixin.qq.com/s?__biz=MzAwNTI1MDE0MA==&mid=2247484300&idx=1&sn=a26771b75cc1cac2b1d9986f66d72706')
