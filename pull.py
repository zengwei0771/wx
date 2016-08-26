#!/usr/bin/env python
#encoding=utf-8


import sys
from bs4 import BeautifulSoup
import json
from datetime import datetime
import time
from models import db, Account, Article
import threading
from proxy import ProxysRequest
from redis import Redis


WORKER_NUM = 2


def get_account_url(account, pr):
    try:
        r = pr.get('http://weixin.sogou.com/weixin?type=1&query=%s&ie=utf8&_sug_=n&_sug_type_=' % account)
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        for item in dom.find('div', class_='results').findAll('div', class_="_item"):
            if item.find('label', attrs={'name': "em_weixinhao"}):
                return item.get('href')
    except Exception, e:
        print 'Exception: %s' % str(e)
    return None


def get_account_lasted_article_urls(account_url, pr):
    results = []
    r = pr.get(account_url)
    if not r:
        return []
    for line in r.content.split('\n'):
        line = line.strip()
        if not line.startswith('var msgList = \''):
            continue

        s = line[15:len(line)-2].replace('&quot;', '"').replace('&nbsp;', ' ')
        s = s.replace('&amp;', '&').replace('&amp;', '&').replace('\\\\', '\\')
        try:
            data = json.loads(s)
            for top in data['list']:
                t = datetime.fromtimestamp(top['comm_msg_info']['datetime'])
                if top['app_msg_ext_info']['is_multi'] == 1:
                    for article in top['app_msg_ext_info']['multi_app_msg_item_list']:
                        results.append({
                            'time': t,
                            'title': article['title'],
                            'cover': article['cover'],
                            'url': article['content_url'],
                            'desc': article['digest'],
                        })
                else:
                    results.append({
                        'time': t,
                        'title': top['app_msg_ext_info']['title'],
                        'cover': top['app_msg_ext_info']['cover'],
                        'url': top['app_msg_ext_info']['content_url'],
                        'desc': top['app_msg_ext_info']['digest'],
                    })
        except Exception, e:
            print 'Exception: %s, %s, %s' % (str(type(e)), str(e), s)
    return results


def get_article_content(article_url, pr):
    try:
        r = pr.get(article_url)
        dom = BeautifulSoup(r.content, "html5lib", from_encoding="UTF-8")
        content = dom.find('div', id='js_content')
        if content:
            return str(content)
    except Exception, e:
        print e
    return None


def start():
    db.connect()
    pr = ProxysRequest([])
    rs = Redis(host='127.0.0.1', port=6379, db=0)

    while True:
        proxys = rs.hgetall('alives')
        pr.update(proxys.keys())

        for account in Account.select():
            account_url = get_account_url(account._id, pr)
            if not account_url:
                continue

            results = get_account_lasted_article_urls(account_url, pr)
            updated = 0
            for arti in results:
                if account.lastest_time and account.lastest_time > arti['time']:
                    continue

                content = get_article_content('http://mp.weixin.qq.com%s' % arti['url'], pr)
                if content is None:
                    print 'get article failed: %s' % arti['url']
                else:
                    Article.create(
                        account=account,
                        title=arti['title'],
                        desc=arti['desc'].replace('&quot;', '"'),
                        content=content,
                        time=arti['time'],
                        cover=arti['cover']
                    )
                    updated += 1
                time.sleep(3)
            if updated > 0:
                print 'update %d article' % updated
                account.count += updated
                account.lastest_time = datetime.now()
                account.save()

            time.sleep(10)

        time.sleep(600)
    db.close()


if __name__ == '__main__':
    start()
