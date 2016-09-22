#!/usr/bin/env python
#encoding=utf-8


from peewee import *
from datetime import datetime
from convertutf8 import ConvertUtf8
from common import write_content, keyword_hot


db = MySQLDatabase('wx', host='127.0.0.1', user='root', passwd='12qwaszx', charset='utf8')


class BaseModel(Model):

    class Meta:
        database = db


class Account(BaseModel):
    __table__ = 'account'

    aid = FixedCharField(primary_key=True, max_length=32)
    name = FixedCharField(max_length=64, null=False)
    desc = CharField(max_length=255)

    @classmethod
    def hots(cls, since):
        accounts = {}
        for a in Article.select().where(Article.date > since):
            if accounts.get(a.account.aid):
                accounts[a.account.aid][1] += a.read
                accounts[a.account.aid][2] += a.agree
            else:
                accounts[a.account.aid] = [a.account.name, a.read, a.agree]
        tops = sorted(accounts.items(), key=lambda x:x[1][1], reverse=True)[:15]
        return [{
            'aid':i[0],
            'account_name':i[1][0],
            'allread':i[1][1],
            'allagree':i[1][2]
        } for i in tops]


class Article(BaseModel):
    __table__ = 'article'

    articleid = PrimaryKeyField()
    titleid = CharField(unique=True, max_length=255, null=False)
    title = CharField(max_length=255, null=False)
    account = ForeignKeyField(Account, to_field='aid')
    desc = CharField(max_length=512)
    cover = CharField(max_length=512)
    source = CharField(max_length=512)
    date = DateField(null=False)
    read = IntegerField(null=False)
    agree = IntegerField(null=False)
    catagory = FixedCharField(max_length=32, null=False)
    catagoryid = FixedCharField(max_length=32, null=False)
    video = CharField(max_length=512)
    vote = FixedCharField(max_length=16, null=True)
    keywords = CharField(max_length=255, null=True)
    index = IntegerField(null=True)

    @classmethod
    def add(cls, source, title, titleid, catagory, arinfo, read, agree):
        account = Account.select().where(Account.aid == arinfo['account_id'])
        if not account:
            account = Account.create(
                aid=arinfo['account_id'],
                name=arinfo['account_name'],
                desc=arinfo['account_desc']
            )
        keywords, hot = keyword_hot(title)
        article = cls.create(
            titleid=titleid,
            title=title,
            account=account,
            desc=arinfo['desc'],
            source=source,
            date=arinfo['date'],
            cover=arinfo['cover'],
            catagory=catagory,
            catagoryid=ConvertUtf8.convert(catagory),
            read=read,
            agree=agree,
            video=arinfo['videos'][0] if arinfo['videos'] else '',
            keywords=','.join(keywords),
            index=hot
        )
        write_content(titleid, arinfo['date'], arinfo['content'])
        for img in arinfo['imgs']:
            Pic.create(
                src=img['src'],
                article=article,
                width=img['width'],
                height=img['height']
            )

    @classmethod
    def catagorys(cls, since):
        catagorys = [i.todict() for i in Article.select(Article.catagoryid, Article.catagory, fn.sum(Article.index).alias('s')).where(Article.date > since).group_by(Article.catagoryid, Article.catagory).order_by(SQL('s desc'))]
        return catagorys

    @classmethod
    def hots(cls, since):
        return [i.todict() for i in Article.select().where(Article.date > since).order_by(Article.index.desc()).limit(15)]

    @classmethod
    def hot_videos(cls, since):
        return [i.todict() for i in Article.select().where(Article.date > since).where(SQL('`video` != ""')).order_by(Article.index.desc()).limit(15)]

    def todict(self):
        r = dict(self._data)
        if 'date' in r.keys():
            r['date'] = r['date'].strftime('%Y-%m-%d')
        return r


class Pic(BaseModel):
    __table__ = 'pic'

    picid = PrimaryKeyField()
    src = CharField(max_length=512, null=False)
    article = ForeignKeyField(Article, to_field='articleid')
    width = IntegerField(null=False)
    height = IntegerField(null=False)


if __name__ == '__main__':
    db.connect()
    db.create_tables([Account, Article, Pic])
    db.close()
