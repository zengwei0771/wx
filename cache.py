#!/usr/bin/env python
#encoding=utf-8


import redis
from models import db, Article, Account
from datetime import datetime, timedelta
import json
import sys


COUNT = 4000
CATAGORY_COUNT = 1000
VIDEO_COUNT = 500
EXPIRE = 1800


def set_content(R, a):
    article_key = 'ARTICLE:%d'
    if not R.get(article_key % a.articleid):
        R.setex(article_key % a.articleid, json.dumps({
            'titleid': a.titleid,
            'title': a.title,
            'account_id': a.account.aid,
            'account_name': a.account.name,
            'article_desc': a.desc,
            'cover': a.cover,
            'date': a.date.strftime('%Y-%m-%d'),
            'read': a.read,
            'agree': a.agree,
            'vote': a.vote,
            'keywords': a.keywords,
            'video': a.video,
        }), EXPIRE)
    else:
        R.expire(article_key % a.articleid, EXPIRE)



def put():
    TODAY = datetime.now().date()
    R = redis.Redis(host='127.0.0.1', port=6379, db=1)
    db.connect()

    print 'hot:articles'
    hots = Article.hots(TODAY - timedelta(7))
    R.set('HOT:ARTICLES', json.dumps(hots))

    print 'hot:videos'
    hot_videos = Article.hot_videos(TODAY - timedelta(7))
    R.set('HOT:VIDEOS', json.dumps(hot_videos))

    print 'catagorys'
    catagorys = Article.catagorys(TODAY - timedelta(7))
    R.set('CATAGORYS', json.dumps(catagorys))

    print 'hot:accounts'
    hot_accounts = Account.hots(TODAY - timedelta(3))
    R.set('HOT:ACCOUNTS', json.dumps(hot_accounts))

    print 'count:all'
    ct = Article.select().count()
    R.set('COUNT:ALL', ct)
    for c in catagorys:
        print 'count:%s' % c['catagoryid'].upper()
        ct = Article.select().where(Article.catagoryid == c['catagoryid']).count()
        R.set('COUNT:%s' % c['catagoryid'].upper(), ct)
    print 'count:video'
    ct = Article.select().where(Article.video != '').count()
    R.set('COUNT:VIDEO', ct)

    for t in ['hot', 'newest', 'hotread', 'hotagree']:
        print 'list:%s' % t
        ls = []
        k = 'LIST:%s' % t.upper()
        seler = Article.select()
        if t == 'hot':
            seler = seler.order_by(Article.date.desc(), Article.index.desc())
        elif t == 'hotread':
            seler = seler.order_by(Article.date.desc(), Article.read.desc())
        elif t == 'hotagree':
            seler = seler.order_by(Article.date.desc(), Article.agree.desc())
        else:
            seler = seler.order_by(Article.date.desc())
        seler = seler.limit(COUNT)
        for a in seler:
            ls.append(a.articleid)
            set_content(R, a)
        R.delete(k)
        [R.lpush(k, i) for i in ls[::-1]]

    print 'list:video'
    ls = []
    k = 'LIST:VIDEO'
    for a in Article.select().where(Article.video != '').order_by(Article.date.desc()).limit(VIDEO_COUNT):
        ls.append(a.articleid)
        set_content(R, a)
    R.delete(k)
    [R.lpush(k, i) for i in ls[::-1]]

    for c in catagorys:
        print 'list:%s' % c['catagoryid']
        ls = []
        k = 'LIST:%s' % c['catagoryid'].upper()
        for a in Article.select().where(Article.catagoryid == c['catagoryid'])\
                .order_by(Article.date.desc(), Article.index.desc())\
                .limit(CATAGORY_COUNT):
            ls.append(a.articleid)
            set_content(R, a)
        R.delete(k)
        [R.lpush(k, i) for i in ls[::-1]]

    db.close()

def reset():
    R = redis.Redis(host='127.0.0.1', port=6379, db=1)
    for k in R.keys():
        print 'clear %s' % k
        R.delete(k)


if __name__ == '__main__':
    if 'reset' in sys.argv:
        reset()
    else:
        put()
