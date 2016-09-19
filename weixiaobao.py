#!/usr/bin/env python
#encoding=utf-8


import sys
reload(sys)
sys.setdefaultencoding('utf-8')
import requests
from models import db, Article, Account, Pic, insert_article
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint
from convertutf8 import ConvertUtf8
from wx import WX
import time
from common import UA, TO, write_content
import shortuuid


URL = 'http://top.wxb.com/article/cat/%d/%s'


def pull_weixiaobao():
    db.connect()
    timestamp = time.time()
    timestamp = timestamp - timestamp%86400
    lastday = datetime.utcfromtimestamp(timestamp) - timedelta(1)
    lastday_str = lastday.strftime('%Y-%m-%d')
    for i in range(1, 25):
        r = None
        for retry in range(3):
            try:
                r = requests.get(URL % (i, lastday_str), headers={'User-Agent':UA}, timeout=TO)
                break
            except Exception, e:
                print e
                time.sleep(30)
        if r is None:
            continue
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        catagory = dom.find('ul', class_='rank-detail-left-nav').find('li', class_='active').text.strip()
        for item in dom.find('ul', class_='rank-list-2').findAll('li'):
            item = item.find('div', class_='normal')
            title = item.find('a', class_='link-title').text.strip()
            title = WX.filter_emoji(title)
            if Article.select().where(Article.title == title):
                print title, 'existed'
                continue
            titleid = shortuuid.uuid(name=title.encode('utf8'))

            print title, 'inserting'
            href = item.find('a', class_='link-title').get('href')
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

            arinfo = WX.article_info(href)
            if not arinfo:
                print 'pull article info failed'
                continue

            insert_article(href, title, titleid, catagory, arinfo, read, agree)
        time.sleep(10)
    db.close()


if __name__ == '__main__':
    pull_weixiaobao()
