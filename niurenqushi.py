#!/usr/bin/env python
#encoding=utf-8


import requests
from models import db, Article, Account, Pic
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint
from convertutf8 import ConvertUtf8
from wx import WX
import time
from common import UA, TO, write_content


URL = 'http://weixin.niurenqushi.com/api/get_article_list/?pageindex=%d&pagesize=500&categoryid=0'


def pull():
    db.connect()
    today = datetime.now().date()
    for page in range(1, 2):
        url = URL % page
        r = None
        for retry in range(3):
            try:
                r = requests.get(url, headers={'User-Agent':UA}, timeout=TO).json()
                break
            except Exception, e:
                print e
                time.sleep(30)
        if r is None:
            continue
        for item in r['item']:
            t = datetime.strptime(item['AddTime'].split('.')[0], '%Y-%m-%dT%H:%M:%S')
            if t.date() < today - timedelta(1):
                return

            title = WX.filter_emoji(item['Title'])
            titleid = ConvertUtf8.convert(title)
            if Article.select().where(Article.titleid == titleid):
                print title, t, 'existed'
                continue

            print title, t, 'inserting'
            arinfo = WX.article_info(item['SourceUrl'])
            if not arinfo:
                print 'pull article info failed', item['SourceUrl']
                continue

            account = Account.select().where(Account.aid == arinfo['account_id'])
            if not account:
                account = Account.create(
                    aid=arinfo['account_id'],
                    name=arinfo['account_name'],
                    desc=arinfo['account_desc']
                )
            read = item['ViewCount'] + randint(0, 3000)
            article = Article.create(
                titleid=titleid,
                title=title,
                account=account,
                desc=WX.filter_emoji(item['Summary']),
                content='',
                source=item['SourceUrl'],
                time=t,
                cover=item['Pic'],
                catagory=item['CategoryName'],
                catagoryid=ConvertUtf8.convert(item['CategoryName']),
                read=read,
                agree=read*0.02+randint(0, 1000)*0.005,
                video=arinfo['videos'][0] if arinfo['videos'] else ''
            )
            write_content(titleid, t, arinfo['content'])
            for img in arinfo['imgs']:
                Pic.create(
                    src=img['src'],
                    article=article,
                    width=img['width'],
                    height=img['height']
                )
    db.close()


if __name__ == '__main__':
    pull()
