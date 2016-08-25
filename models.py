#!/usr/bin/env python
#encoding=utf-8


from peewee import *
from datetime import datetime


db = MySQLDatabase('wx', host='127.0.0.1', user='root', passwd='12qwaszx', charset='utf8mb4')


class BaseModel(Model):

    class Meta:
        database = db


class Account(BaseModel):
    __table__ = 'account'

    _id = CharField(primary_key=True, max_length=64, unique=True, null=False)
    name = CharField(max_length=256, null=False)
    desc = CharField(max_length=1024)

    count = IntegerField(default=0, null=False)
    lastest_time = DateTimeField(null=True)


class Article(BaseModel):
    __table__ = 'article'

    _id = PrimaryKeyField()
    account = ForeignKeyField(Account, on_delete='CASCADE')
    title = CharField(max_length=256, null=False)
    desc = CharField(max_length=1024)
    content = TextField(null=False)
    cover = CharField(max_length=1024)
    time = DateTimeField(null=False)


if __name__ == '__main__':
    db.connect()
    db.create_tables([Article])
    db.close()
