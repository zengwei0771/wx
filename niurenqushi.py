#!/usr/bin/env python
#encoding=utf-8


import requests
from models import db, Article
from datetime import datetime, timedelta
from bs4 import BeautifulSoup
from random import randint


URL = 'http://weixin.niurenqushi.com/api/get_article_list/?pageindex=%d&pagesize=500&categoryid=0'
UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
TO = 8


def pull():
    db.connect()
    today = datetime.now().date()
    for page in range(1, 5):
        url = URL % page
        r = requests.get(url, headers={'User-Agent':UA}, timeout=TO).json()
        for item in r['item']:
            t = datetime.strptime(item['AddTime'].split('.')[0], '%Y-%m-%dT%H:%M:%S')
            if t.date() < today - timedelta(1):
                return

            print item['Title'], t
            try:
                c = requests.get(item['SourceUrl'], headers={'User-Agent':UA}, timeout=TO)
                cdom = BeautifulSoup(c.content, "html5lib", from_encoding="UTF-8")
                content = cdom.find('div', id='js_content')
                if not content:
                    continue
                read = int(item['ViewCount']) + randint(0, 4000)
                Article.create(
                    account=item['ChannelName'],
                    title=item['Title'],
                    desc=item['Summary'],
                    content=str(content),
                    time=t,
                    cover=item['Pic'],
                    _type=item['CategoryName'],
                    read=read,
                    agree=read*0.02+randint(0, 1000)*0.01
                )
            except Exception, e:
                print e
    db.close()


if __name__ == '__main__':
    pull()
