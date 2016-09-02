#!/usr/bin/env python
#encoding=utf-8

import sys
reload(sys)
sys.setdefaultencoding('utf8')
import codecs


class ConvertUtf8():

    __DATA__ = {}

    @staticmethod
    def init():
        with codecs.open('ziku', 'r', 'utf8') as f:
            while True:
                line = f.readline()
                if not line:
                    break

                line = line.strip().split('|')
                ConvertUtf8.__DATA__[line[0]] = '%s%s' % (line[1], line[2])

    @staticmethod
    def convert(source):
        if type(source) is not unicode:
            source = source.decode('utf8')
        dest = ''
        for i in source:
            if ConvertUtf8.__DATA__.get(i):
                dest += ConvertUtf8.__DATA__[i]
        return ''.join([ConvertUtf8.__DATA__[i] if ConvertUtf8.__DATA__.get(i) else 'nan' for i in source])


ConvertUtf8.init()


if __name__ == '__main__':
    print ConvertUtf8.convert('澳大利亚南海跳得太欢，没想到报应来得这么快！')
