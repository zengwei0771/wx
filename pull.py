#!/usr/bin/env python
#encoding=utf-8


import requests
from bs4 import BeautifulSoup
import json
from datetime import datetime
import time


HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
}
TO = 5

accounts = [
    'gxzh01',
]


def get_accounts():
    accounts = {}
    headers = {
        'User-Agent': HEADERS['User-Agent']
    }
    for i in range(48, 58) + range(97, 123):
        c = chr(i)
        n = 0
        while True:
            print 'q=%c&lastindex=%d' % (c, n)
            headers['Referer'] = 'http://chuansong.me/search?q=%s' % c
            r = requests.get('http://chuansong.me/more/search/account?q=%s&t=account&lastindex=%d' % (c, n), headers=headers)
            dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
            if dom.text.strip() == '0':
                break

            if len(dom.findAll('div', class_='user_query_result')) == 0:
                print '访问频率过快，等10s重试'
                time.sleep(10)
                continue

            for item in dom.findAll('div', class_='user_query_result'):
                _id = item.find('a').get('href').rsplit('/', 1)[1]
                if accounts.get(_id):
                    continue
                name = item.find('a', class_='user').text.strip().split(' (')[0]
                desc = item.find('div', class_='search_result_snippet').text.strip()
                accounts[_id] = {
                    'name': name,
                    'desc': desc
                }
            n += 10
            time.sleep(3)


def get_account_url(account):
    try:
        r = requests.get('http://weixin.sogou.com/weixin?type=1&query=%s&ie=utf8&_sug_=n&_sug_type_=' % account, headers=HEADERS, timeout=TO)
        dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
        for item in dom.find('div', class_='results').findAll('div', class_="_item"):
            if item.find('label', attrs={'name': "em_weixinhao"}):
                return item.get('href')
    except Exception, e:
        print e
    return None


def get_account_lasted_article_urls(account_url):
    results = []
    try:
        r = requests.get(account_url, headers=HEADERS, timeout=TO)
        for line in r.content.split('\n'):
            line = line.strip()
            if line .startswith('var msgList = \''):
                s = line[15:len(line)-2].replace('&quot;', '"')
                s = s.replace('&amp;', '&').replace('&amp;', '&').replace('\\\\', '\\')
                data = json.loads(s)
                for top in data['list']:
                    t = datetime.fromtimestamp(top['comm_msg_info']['datetime'])
                    for article in top['app_msg_ext_info']['multi_app_msg_item_list']:
                        results.append({
                            'time': t,
                            'title': article['title'],
                            'cover': article['cover'],
                            'url': article['content_url'],
                            'desc': article['digest'],
                        })
    except Exception, e:
        print e
    return results


if __name__ == '__main__':
    #account_url = get_account_url(accounts[0])
    #print account_url
    #print get_account_lasted_article_urls(account_url)

    get_accounts()
