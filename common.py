#!/usr/bin/env python
#encoding=utf-8


import os


UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36'
TO = 20
CONTENT_DIR = './pages/contents/'


def write_content(titleid, t, content):
    p = CONTENT_DIR + t.strftime('%Y-%m-%d')
    if not os.path.exists(p):
        os.mkdir(p)

    with open('%s/%s.html' % (p, titleid), 'w') as f:
        f.write(content.encode('utf8'))
