#!/usr/bin/env python
#encoding=utf-8


import sys
reload(sys)
sys.setdefaultencoding('utf-8')
import requests
from models import db, Article, Account, Pic
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint
from convertutf8 import ConvertUtf8
from wx import WX
import time
from common import UA, TO, write_content
import shortuuid
from datetime import datetime, timedelta


URL = 'http://top.aiweibang.com/article/daily/class?k=%d&t=%s'


def pull(t):
    db.connect()
    for i in range(1, 60):
        r = None
        for retry in range(3):
            try:
                r = requests.get(URL % (i, t.strftime('%Y-%m-%d')), headers={'User-Agent':UA}, timeout=TO)
                break
            except Exception, e:
                print e
                time.sleep(30)
        if r is None:
            continue
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        if not dom.find('div', class_='msg-main'):
            continue
        catagory = dom.find('div', id="rank_name").text.strip().split('』', 1)[0].split('『', 1)[1]
        if not catagory:
            catagory = '综合'
        for item in dom.find('div', class_='msg-main').findAll('div', class_='msg-item'):
            a = item.find('div', class_='title').find('a')
            title = a.get('title')
            title = WX.filter_emoji(title)
            if Article.select().where(Article.title == title):
                print title, 'existed'
                continue
            titleid = shortuuid.uuid(name=title.encode('utf8'))

            print title, 'inserting'
            href = a.get('href')
            nums = [i.strip() for i in item.find('span', class_='num').text.strip().split(' ') if i.strip()]
            if nums[0].endswith('+'):
                read = int(nums[0][:len(nums[0])-1])
            else:
                read = int(nums[0])
            if nums[1].endswith('+'):
                agree = int(nums[1][:len(nums[1])-1])
            else:
                agree = int(nums[1])

            arinfo = WX.article_info(href)
            if not arinfo:
                print 'pull article info failed'
                continue

            Article.add(href, title, titleid, catagory, arinfo, read, agree)
        time.sleep(10)
    db.close()


if __name__ == '__main__':
    t = datetime.now()
    for i in range(1, 4):
        print t.strftime('%Y-%m-%d')
        pull(t-timedelta(i))
