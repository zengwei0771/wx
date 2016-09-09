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
    content = TextField(null=False)
    cover = CharField(max_length=512)
    source = CharField(max_length=512)
    date = DateField(null=False)
    read = IntegerField(null=False)
    agree = IntegerField(null=False)
    catagory = FixedCharField(max_length=32, null=False)
    catagoryid = FixedCharField(max_length=32, null=False)
    video = CharField(max_length=512)


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
