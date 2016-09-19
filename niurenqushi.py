#!/usr/bin/env python
#encoding=utf-8


import sys
reload(sys)
sys.setdefaultencoding('utf-8')
import requests
from models import db, Article, Account, Pic, insert_article
from bs4 import BeautifulSoup
from random import randint
from convertutf8 import ConvertUtf8
from wx import WX
import time
from common import UA, TO, write_content
import shortuuid


URL = 'http://weixin.niurenqushi.com/api/get_article_list/?pageindex=%d&pagesize=500&categoryid=0'


def pull():
    db.connect()
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
            title = WX.filter_emoji(item['Title'])
            if Article.select().where(Article.title == title):
                print title, 'existed'
                continue
            titleid = shortuuid.uuid(name=title.encode('utf8'))

            print title, 'inserting'
            arinfo = WX.article_info(item['SourceUrl'])
            if not arinfo:
                print 'pull article info failed', item['SourceUrl']
                continue

            read = item['ViewCount'] + randint(0, 3000)
            agree = read*0.02+randint(0, 1000)*0.005
            insert_article(item['SourceUrl'], title, titleid, item['CategoryName'], arinfo, read, agree)
    db.close()


if __name__ == '__main__':
    pull()
