#!/usr/bin/env python
#encoding=utf-8


import requests
from models import db, Article
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint


UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
TO = 8


def pull_weixiaobao():
    db.connect()
    lastday = datetime.now() - timedelta(1)
    lastday_str = lastday.strftime('%Y-%m-%d')
    for i in range(1, 25):
        r = requests.get('http://top.wxb.com/article/cat/%d/%s' % (i, lastday_str), headers={'User-Agent':UA}, timeout=TO)
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        _type = dom.find('ul', class_='rank-detail-left-nav')\
                .find('li', class_='active').text.strip()
        for item in dom.find('ul', class_='rank-list-2').findAll('li'):
            item = item.find('div', class_='normal')
            title = item.find('a', class_='link-title').text.strip()
            href = item.find('a', class_='link-title').get('href')
            account = item.find('span', class_='weixin-name').text.strip()
            read = item.find('span', class_='read-num').text.strip()
            if not read.endswith('+'):
                read = int(read)
            else:
                read = int(read[:len(read)-1])
            agree = item.find('span', class_='praise-num').text.strip()
            if not agree.endswith('+'):
                agree = int(agree)
            else:
                agree = int(agree[:len(agree)-1])
            t = lastday + timedelta(seconds=randint(0, 86400))
            print title, t
            try:
                c = requests.get(href, headers={'User-Agent':UA}, timeout=TO)
                cdom = BeautifulSoup(c.content, "html5lib", from_encoding="UTF-8")
                content = cdom.find('div', id='js_content')
                if not content:
                    continue
                cover = ''
                for img in content.findAll('img'):
                    cover = img.get('data-src')
                    if cover.endswith('jpeg'):
                        break
                desc = ''
                for line in content.text.strip().split('\n'):
                    desc += line.strip() + ' '
                    if len(desc) > 160:
                        break
                desc = desc[:400]
                Article.create(
                    account=account,
                    title=title,
                    desc=desc,
                    content=str(content),
                    time=t,
                    cover=cover,
                    _type=_type,
                    read=read,
                    agree = agree
                )
            except Exception, e:
                print e
    db.close()


if __name__ == '__main__':
    pull_weixiaobao()
