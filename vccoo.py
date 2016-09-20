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
from wx import WX
import time
from common import UA, TO
import shortuuid


INDEX_URL = 'http://www.vccoo.com/'


def pull_vccoo():
    db.connect()
    r = requests.get(INDEX_URL, headers={'User-Agent':UA}, timeout=TO)
    dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
    for li in dom.find('div', class_='side-menu').findAll('li'):
        list_url = li.find('a').get('href')
        print 'list url: ' + list_url
        out = False
        for i in range(1, 3):
            lr = requests.get('%s&page=%d' % (list_url, i), headers={'User-Agent':UA}, timeout=TO)
            ldom = BeautifulSoup(lr.content, "html5lib", from_encoding="UTF-8")
            catagory = ldom.find('div', class_='crumbs').find('span', class_='last').text.strip()
            if not catagory:
                catagory = '综合'
            print 'catagory: ' + catagory
            for item in ldom.findAll('div', class_='classify-list-con'):
                if '分钟' not in item.find('p', class_='list-infor').text:
                    out = True
                    break
                a = item.find('div', class_='list-con').find('a')
                title = WX.filter_emoji(a.get('title'))
                if Article.select().where(Article.title == title):
                    print title, 'existed'
                    continue
                titleid = shortuuid.uuid(name=title.encode('utf8'))
                print title, 'inserting'

                article_url = a.get('href')
                print 'article_url: ' + article_url
                ar = requests.get(article_url, headers={'User-Agent':UA}, timeout=TO)
                source = None
                for line in ar.content.split('\n'):
                    if line.strip().startswith('var s = "http'):
                        source = line.strip()[9:-2]
                        break
                print 'source: ' + source
                if source:
                    arinfo = WX.article_info(source)
                    if not arinfo:
                        print 'pull article info failed'
                        continue
                    read = randint(0, 500)
                    agree = read*0.02 + randint(0, 1000)*0.005
                    Article.add(source, title, titleid, catagory, arinfo, read, agree)
                else:
                    print 'get source failed'
                    continue
            if out:
                break
        time.sleep(10)
    db.close()


if __name__ == '__main__':
    pull_vccoo()
