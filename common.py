#!/usr/bin/env python
#encoding=utf-8


import os
from hashlib import md5
import jieba
import requests


UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
TO = 20
CONTENT_DIR = './pages/contents/'


def write_content(titleid, d, content):
    p = CONTENT_DIR + d.strftime('%Y-%m-%d')
    if not os.path.exists(p):
        os.mkdir(p)

    with open('%s/%s.html' % (p, titleid), 'w') as f:
        f.write(content.encode('utf8'))


def keyword_hot(s):
    words = [i for i in jieba.cut_for_search(s) if i]
    words = sorted(words, key=lambda x:len(x), reverse=True)[:5]
    url = u'http://index.so.com/index.php?a=overviewJson&q=%s&area=全国' % ','.join(words)
    index = 0
    try:
        r = requests.get(url, headers={'User-Agent':UA}, timeout=TO).json()
        for item in r['data']:
            index += item['data']['week_index']
    except Exception, e:
        print 'search 360 hot index failed: %s, %s' % (url, str(e))
    print index
    return words, index
