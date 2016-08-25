#!/usr/bin/env python
#encoding=utf-8


import sys
import requests
from bs4 import BeautifulSoup
import json
from datetime import datetime
import time
from models import db, Account, Article
import re


HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
}
TO = 5
S = requests.Session()


def get_proxys():
    proxys = []
    try:
        r = requests.get('http://www.xicidaili.com/nn', headers=HEADERS)
        dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
        for tr in dom.find('table', id='ip_list').findAll('tr'):
            tds = tr.findAll('td')
            if len(tds) == 0 or tds[5].text.strip() != 'HTTP':
                continue
            proxys.append('http://%s:%s' % (tds[1].text.strip(), tds[2].text.strip()))
    except Exception, e:
        print e
    return proxys


def get_accounts(start_c='0', start_n=0):
    db.connect()
    exists = set()
    for item in Account.select():
        exists.add(item._id)

    headers = {
        'User-Agent': HEADERS['User-Agent']
    }
    proxys = get_proxys()
    proxys.insert(0, 'http://127.0.0.1:80')
    for i in range(48, 58) + range(97, 123):
        if i < ord(start_c):
            continue
        c = chr(i)
        if c == start_c:
            n = start_n
        else:
            n = 0
        while True:
            print 'q=%c&lastindex=%d' % (c, n)
            headers['Referer'] = 'http://chuansong.me/search?q=%s' % c
            try:
                if proxys[0] == 'http://127.0.0.1:80':
                    r = requests.get('http://chuansong.me/more/search/account?q=%s&t=account&lastindex=%d' % (c, n), headers=headers, timeout=TO)
                else:
                    r = S.get('http://chuansong.me/more/search/account?q=%s&t=account&lastindex=%d' % (c, n), headers=headers, proxies={'http':proxys[0]}, timeout=10)
            except Exception, e:

                print type(e), e
                time.sleep(10)
                print 'remove proxy %s' % proxys[0]
                proxys.pop(0)
                continue
            dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
            if dom.text.strip() == '0':
                break

            if len(dom.findAll('div', class_='user_query_result')) == 0:
                print 'too fast，retry waiting for 10s'
                time.sleep(10)
                continue

            for item in dom.findAll('div', class_='user_query_result'):
                _id = item.find('a').get('href').rsplit('/', 1)[1]
                if _id in exists:
                    continue
                name = item.find('a', class_='user').text.strip().split(' (')[0]
                desc = item.find('div', class_='search_result_snippet').text.strip()
                exists.add(_id)
                try:
                    Account.create(_id=_id, name=name, desc=desc)
                except Exception, e:
                    print 'Exception: %s, %s, %s, %s' % (str(e), _id, name, desc)
            n += 10
            time.sleep(3)
    db.close()


def get_account_url(account, proxys):
    while len(proxys) > 0:
        try:
            if len(proxys) == 0 or proxys[0] == 'http://127.0.0.1:80':
                r = requests.get('http://weixin.sogou.com/weixin?type=1&query=%s&ie=utf8&_sug_=n&_sug_type_=' % account, headers=HEADERS, timeout=TO)
            else:
                r = S.get('http://weixin.sogou.com/weixin?type=1&query=%s&ie=utf8&_sug_=n&_sug_type_=' % account, headers=HEADERS, proxies={'http':proxys[0]}, timeout=10)
            if '用户您好，您的访问过于频繁' in r.content:
                proxys.pop(0)
                print 'sogou weixin occur validator, chang proxy'
                continue

            dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
            for item in dom.find('div', class_='results').findAll('div', class_="_item"):
                if item.find('label', attrs={'name': "em_weixinhao"}):
                    return item.get('href')
        except Exception, e:
            print 'Exception: %s' % str(e)
            proxys.pop(0)
            continue
        break
    return None


def get_account_lasted_article_urls(account_url):
    results = []
    try:
        r = requests.get(account_url, headers=HEADERS, timeout=TO, allow_redirects=True)
        for line in r.content.split('\n'):
            line = line.strip()
            if line .startswith('var msgList = \''):
                s = line[15:len(line)-2].replace('&quot;', '"').replace('&nbsp;', ' ')
                s = s.replace('&amp;', '&').replace('&amp;', '&').replace('\\\\', '\\')
                try:
                    data = json.loads(s)
                except Exception, e:
                    print 'Exception: %s, %s, %s' % (str(type(e)), str(e), s)
                    break
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
        print e
    return results


def get_article_content(article_url):
    try:
        r = requests.get(article_url, headers=HEADERS, timeout=TO, allow_redirects=True)
        dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
        content = dom.find('div', id='js_content')
        if content:
            return str(content)
    except Exception, e:
        print e
    return None


def get_articles():
    proxys = get_proxys()
    proxys.insert(0, 'http://127.0.0.1:80')

    db.connect()
    for account in Account.select():
        if len(proxys) == 0:
            break

        account_url = get_account_url(account._id, proxys)
        print 'account id url: %s, %s' % (account._id, account_url)
        if account_url is not None:
            results = get_account_lasted_article_urls(account_url)
            updated = 0
            for arti in results:
                if account.lastest_time and account.lastest_time > arti['time']:
                    continue
                content = get_article_content('http://mp.weixin.qq.com%s' % arti['url'])
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
            if updated > 0:
                print 'update %d article' % updated
                account.count += updated
                account.lastest_time = datetime.now()
                account.save()
        time.sleep(1)
    db.close()


if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1] == 'account':
        if len(sys.argv) == 4:
            get_accounts(sys.argv[2], int(sys.argv[3]))
        else:
            get_accounts('0', 0)
    elif len(sys.argv) == 2 and sys.argv[1] == 'proxy':
        print get_proxys()
    elif len(sys.argv) == 2 and sys.argv[1] == 'test':
        proxys = get_proxys()
        proxys.insert(0, 'http://127.0.0.1:80')
        account_url = get_account_url('a11096988', proxys)
        print account_url
        print get_account_lasted_article_urls(account_url)
    else:
        #= get_account_url('xxcl1985v')
        #print account_url
        #print get_account_lasted_article_urls(account_url)
        get_articles()
