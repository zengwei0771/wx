#!/usr/bin/env python
#encoding=utf-8


from peewee import *
from datetime import datetime


db = MySQLDatabase('wx', host='127.0.0.1', user='root', passwd='12qwaszx', charset='utf8')


class BaseModel(Model):

    class Meta:
        database = db


class Account(BaseModel):
    __table__ = 'account'

    aid = CharField(primary_key=True, max_length=64)
    name = CharField(max_length=256, null=False)
    desc = CharField(max_length=1024)


class Article(BaseModel):
    __table__ = 'article'

    articleid = PrimaryKeyField()
    titleid = CharField(unique=True, max_length=512, null=False)
    title = CharField(max_length=1024, null=False)
    account = ForeignKeyField(Account, to_field='aid')
    desc = CharField(max_length=1024)
    content = TextField(null=False)
    cover = CharField(max_length=1024)
    source = CharField(max_length=2014)
    time = DateTimeField(null=False)
    read = IntegerField(null=False)
    agree = IntegerField(null=False)
    catagory = CharField(max_length=64, null=False)
    catagoryid = CharField(max_length=64, null=False)
    video = CharField(max_length=1024)


class Pic(BaseModel):
    __table__ = 'pic'

    picid = PrimaryKeyField()
    src = CharField(max_length=1024, null=False)
    article = ForeignKeyField(Article, to_field='articleid')
    width = IntegerField(null=False)
    height = IntegerField(null=False)


if __name__ == '__main__':
    db.connect()
    db.create_tables([Account, Article, Pic])
    db.close()
