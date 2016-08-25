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


logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s %(filename)s[line:%(lineno)d] %(levelname)s %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
    stream=sys.stdout,
)


class ProxysRequest(object):

    def __init__(self, proxys):
        self.proxys = {}
        for p in proxys[:100]:
            self.proxys[p] = {
                'valid': True,
                'priority': 0
            }

    def get(self, url, headers, timeout=8):
        if len(self.proxys) == 0:
            return None

        ps = sorted([i for i in self.proxys.items() if i[1]['valid']],
                    key=lambda x:x[1]['priority'])
        for p in ps:
            s = requests.Session()
            try:
                r = s.get(url,
                          headers=headers,
                          proxies={'http': p[0]},
                          timeout=timeout)
                if r.status_code == 200:
                    p[1]['priority'] += 1
                    return r
            except Exception, e:
                logging.warn('Proxy dead. %s. %s' % (p, str(e)))
            p[1]['valid'] = False
        return None

    def update(self, proxys):
        for p in proxys[:100]:
            if not self.proxys.get(p) or not self.proxys[p]['valid']:
                self.proxys[p] = {'valid': True, 'priority': 0}


if __name__ == '__main__':
    #print Proxys.get_proxys()
    #print Proxys.test_proxy('http://39.88.192.207:81')
    print Proxys.fetch_xici(['http://60.13.74.143:80'])
