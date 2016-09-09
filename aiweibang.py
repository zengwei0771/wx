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


URL = 'http://top.aiweibang.com/article/daily/class?k=%d&t='


def pull():
    db.connect()
    timestamp = time.time()
    timestamp = timestamp - timestamp%86400
    lastday = datetime.utcfromtimestamp(timestamp) - timedelta(1)
    for i in range(1, 60):
        r = None
        for retry in range(3):
            try:
                r = requests.get(URL % i, headers={'User-Agent':UA}, timeout=TO)
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
            titleid = ConvertUtf8.convert(title)[:224]
            d = lastday.date()
            if Article.select().where(Article.titleid == titleid):
                print title, d, 'existed'
                continue

            print title, d, 'inserting'
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

            account = Account.select().where(Account.aid == arinfo['account_id'])
            if not account:
                account = Account.create(
                    aid=arinfo['account_id'],
                    name=arinfo['account_name'],
                    desc=arinfo['account_desc']
                )
            article = Article.create(
                titleid=titleid,
                title=title,
                account=account,
                desc=arinfo['desc'],
                content='',
                source=href,
                date=d,
                cover=arinfo['cover'],
                catagory=catagory,
                catagoryid=ConvertUtf8.convert(catagory),
                read=read,
                agree=agree,
                video=arinfo['videos'][0] if arinfo['videos'] else ''
            )
            write_content(titleid, d, arinfo['content'])
            for img in arinfo['imgs']:
                Pic.create(
                    src=img['src'],
                    article=article,
                    width=img['width'],
                    height=img['height']
                )
        time.sleep(10)
    db.close()


if __name__ == '__main__':
    pull()
