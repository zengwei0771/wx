#!/usr/bin/env python
#encoding=utf-8


import jieba
import requests
from common import UA, TO


sentences = [
    '「文字馆」想到就走，一个人去鸟巢朝圣',
    '【新闻】又發糖啦！SJ銀赫東海再曝「愛意滿滿」合照',
    '五月天温柔最感动现场 | 把这篇文章，传给你喜欢的人',
    '读书论世 | 从幻想中提取未来',
    '【荐读】一写就错的150个词语，你错了几个',
    '假警察真骗局！去韩国签证咋忽悠走西安女娃四万元？',
    '不可错过！五条件选18只错杀股四大边际拐点或现',
    '【90后诗歌大展第一回】玉珍·田野上的皇后',
    '妈妈必知！你绝对不知道的新生儿皮肤护理。',
    '地产老总没远见，你加班累死也白搭，赶紧辞职',
    '女人，找错了人，就等于输了全部！',
    '解决难民危机：听听中国总理怎么说和做',
    '你还在网购轮胎？！这位奔驰车主已经被坑惨了',
    '灯红酒绿：为什么古人认为酒是绿色的？',
    '700万人围观的国产机器人大战到底有多刺激？',
    '李广除了是飞将军，还创造了一个民族',
    '若我变成陶菲克，哪怕只有一回合',
    '女人有多漂亮，男人就有多喜欢',
    '八问八答：经济学家犀利点评新能源汽车产业政策勉强及格',
    '【内涵段子】运气真好，居然约到这么清纯的妹子！',
    '被骗了，原来白骨精最爱的人是悟空',
    '只说婚纱照有点贵，却不知我们一天有多累',
    '滴滴优步正处合并过渡期你被恶意刷单了吗？',
    '高文安设计--桂林水印長廊酒店度假別墅',
    '互联网推广手段CPS（按销量付费）都有哪些坑？',
    '分手',
]


for s in sentences:
    words = [i for i in jieba.cut_for_search(s) if i]
    words = sorted(words, key=lambda x:len(x), reverse=True)[:5]
    print '|'.join(words)
    url = u'http://index.so.com/index.php?a=overviewJson&q=%s&area=全国' % ','.join(words)
    index = 0
    for item in requests.get(url, headers={'User-Agent':UA}, timeout=TO).json()['data']:
        index += item['data']['week_index']
    print s, index
