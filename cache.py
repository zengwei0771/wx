#!/usr/bin/env python
#encoding=utf-8


import redis
from models import db, Article, Account
from datetime import datetime, timedelta
import json


def put():
    TODAY = datetime.now().date()
    R = redis.Redis(host='127.0.0.1', port=6379, db=1)
    db.connect()

    hots = Article.hots(TODAY - timedelta(7))
    R.set('HOT:ARTICLES', json.dumps(hots))

    hot_videos = Article.hot_videos(TODAY - timedelta(7))
    R.set('HOT:VIDEOS', json.dumps(hot_videos))

    catagorys = Article.catagorys(TODAY - timedelta(7))
    R.set('CATAGORYS', json.dumps(catagorys))

    hot_accounts = Account.hots(TODAY - timedelta(3))
    R.set('HOT:ACCOUNTS', json.dumps(hot_accounts))

    db.close()


if __name__ == '__main__':
    put()
