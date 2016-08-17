#!/usr/bin/env python
#encoding=utf-8


import requests
from bs4 import BeautifulSoup

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
}
TO = 5

accounts = [
    'gxzh01',
]

def search(account):
    try:
        r = requests.get('http://weixin.sogou.com/weixin?type=1&query=%s&ie=utf8&_sug_=n&_sug_type_=' % account, headers=HEADERS, timeout=TO)
        dom = BeautifulSoup(r.content, "html.parser", from_encoding="UTF-8")
        for item in dom.find('div', class_='results').findAll('div', class_="_item"):
            if item.find('label', attrs={'name': "em_weixinhao"}):
                url = item.get('href')
                r = requests.get(url, headers=HEADERS, timeout=TO)
                print account_url
                return
    except Exception, e:
        print e


if __name__ == '__main__':
    search(accounts[0])
