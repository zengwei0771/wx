#!/usr/bin/env python
#encoding=utf-8

__author__ = 'lucky'


import requests
import logging
from bs4 import BeautifulSoup
from datetime import datetime, timedelta
import sys
import threading
from Queue import Queue


HEADERS = {                                                                        
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36',
}


class ProxysRequest(object):

    def __init__(self, proxys):
        self.proxys = {}
        for p in proxys[:100]:
            self.proxys[p] = {
                'valid': True,
                'priority': 0
            }

    def get(self, url, headers=HEADERS, timeout=8, allow_redirects=True):
        if len(self.proxys) == 0:
            return None

        ps = sorted([i for i in self.proxys.items() if i[1]['valid']],
                    key=lambda x:x[1]['priority'])
        for p in ps:
            print url, p
            s = requests.Session()
            try:
                r = s.get(url,
                          headers=headers,
                          proxies={'http': p[0]},
                          timeout=timeout,
                          allow_redirects=allow_redirects)
                print r.content
                print '用户您好，您的访问过于频繁' not in r.content
                if r.status_code == 200 and '用户您好，您的访问过于频繁' not in r.content:
                    p[1]['priority'] += 1
                    return r
            except Exception, e:
                print 'Proxy dead. %s. %s' % (p, str(e))
            p[1]['valid'] = False
        return None

    def update(self, proxys):
        for p in proxys[:100]:
            if not self.proxys.get(p) or not self.proxys[p]['valid']:
                self.proxys[p] = {'valid': True, 'priority': 0}


if __name__ == '__main__':
    pass
