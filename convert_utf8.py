#!/usr/bin/env python
#encoding=utf-8


class ConvertUtf8():

    __DATA__ = {}

    @staticmethod
    def init():
        with open('convert-utf-8.txt', 'r') as f:
            while True:
                line = f.readline()
                if not line:
                    break

                line = line.strip().split(',')[0]
                for i in range(len(line)):
                    if ord(line[i]) < 127:
                        break
                ConvertUtf8.__DATA__[line[:i]] = {
                    'pinyin': line[i:len(line)-1],
                    'shengmu': line[len(line)-1]
                }
                


if __name__ == '__main__':
    ConvertUtf8.init()
