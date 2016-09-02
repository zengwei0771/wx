#!/usr/bin/env python
#encoding=utf-8


import requests
from models import db, Article, Account
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint
from convertutf8 import ConvertUtf8
from wx import WX
import time
from config import UA, TO


URL = 'http://top.wxb.com/article/cat/%d/%s'


def pull_weixiaobao():
    db.connect()
    lastday = datetime.now() - timedelta(1)
    lastday_str = lastday.strftime('%Y-%m-%d')
    for i in range(1, 25):
        r = requests.get(URL % (i, lastday_str), headers={'User-Agent':UA}, timeout=TO)
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        catagory = dom.find('ul', class_='rank-detail-left-nav').find('li', class_='active').text.strip()
        for item in dom.find('ul', class_='rank-list-2').findAll('li'):
            item = item.find('div', class_='normal')
            title = item.find('a', class_='link-title').text.strip()
            titleid = ConvertUtf8.convert(title)
            t = lastday + timedelta(seconds=randint(0, 86400))
            if Article.select().where(Article.titleid == titleid):
                print title, t, 'existed'
                continue

            print title, t, 'inserting'
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

            account = Account.select().where(Account.aid == arinfo['account_id'])
            if not account:
                account = Account.create(
                    aid=arinfo['account_id'],
                    name=arinfo['account_name'],
                    desc=arinfo['account_desc']
                )
            Article.create(
                titleid=titleid,
                title=title,
                account=account,
                desc=arinfo['desc'],
                content=arinfo['content'],
                source=href,
                time=t,
                cover=arinfo['cover'],
                catagory=catagory,
                catagoryid=ConvertUtf8.convert(catagory),
                read=read,
                agree=agree,
                video=arinfo['videos'][0] if arinfo['videos'] else ''
            )
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
    pull_weixiaobao()
