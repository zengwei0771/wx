#!/usr/bin/env python
#encoding=utf-8


from peewee import *
from datetime import datetime
from convertutf8 import ConvertUtf8
from common import write_content


db = MySQLDatabase('wx', host='127.0.0.1', user='root', passwd='12qwaszx', charset='utf8')


class BaseModel(Model):

    class Meta:
        database = db


class Account(BaseModel):
    __table__ = 'account'

    aid = FixedCharField(primary_key=True, max_length=32)
    name = FixedCharField(max_length=64, null=False)
    desc = CharField(max_length=255)


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


class Pic(BaseModel):
    __table__ = 'pic'

    picid = PrimaryKeyField()
    src = CharField(max_length=512, null=False)
    article = ForeignKeyField(Article, to_field='articleid')
    width = IntegerField(null=False)
    height = IntegerField(null=False)


def insert_article(source, title, titleid, catagory, arinfo, read, agree):
    account = Account.select().where(Account.aid == arinfo['account_id'])
    if not account:
        account = Account.create(
            aid=arinfo['account_id'],
            name=arinfo['account_name'],
            desc=arinfo['account_desc']
        )
    article = Article.create(
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
        video=arinfo['videos'][0] if arinfo['videos'] else ''
    )
    write_content(titleid, arinfo['date'], arinfo['content'])
    for img in arinfo['imgs']:
        Pic.create(
            src=img['src'],
            article=article,
            width=img['width'],
            height=img['height']
        )

if __name__ == '__main__':
    db.connect()
    db.create_tables([Account, Article, Pic])
    db.close()
