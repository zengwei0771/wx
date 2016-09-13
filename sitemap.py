#!/usr/bin/env python
#encoding=utf-8


from models import db, Article
from xml.dom.minidom import Document
from datetime import datetime, timedelta


def build(site, path, dateoffset):
    today = datetime.now().date()
    urls = []
    urls.append({
        'loc': 'http://'+site+'/',
        'lastmod': today.strftime('%Y-%m-%d'),
        'changefreq': 'always',
        'priority': 1
    })
    for u in ['hotread', 'recommend', 'hotagree', 'videos']:
        urls.append({
            'loc': 'http://%s/%s/' % (site, u),
            'lastmod': today.strftime('%Y-%m-%d'),
            'changefreq': 'always',
            'priority': 0.8
        })

    db.connect()
    catagorys = set()
    articles = []
    for article in Article.select().where(Article.date>=today-timedelta(dateoffset)).order_by(Article.date.desc()).limit(5000):
        catagorys.add(article.catagoryid)
        articles.append(article)
    for catagoryid in catagorys:
        urls.append({
            'loc': 'http://%s/catagory/%s/' % (site, catagoryid),
            'lastmod': today.strftime('%Y-%m-%d'),
            'changefreq': 'always',
            'priority': 0.6
        })
    for article in articles:
        urls.append({
            'loc': 'http://%s/barticle/%s.html' % (site, article.titleid),
            'lastmod': article.date.strftime('%Y-%m-%d'),
            'changefreq': 'never',
            'priority': 0.4
        })
    db.close()

    doc = Document()
    urlsetnode = doc.createElement('urlset')
    doc.appendChild(urlsetnode)
    for url in urls:
        urlnode = doc.createElement('url')

        locnode = doc.createElement('loc')
        locnode.appendChild(doc.createTextNode(url['loc']))
        urlnode.appendChild(locnode)

        lastmodnode = doc.createElement('lastmod')
        lastmodnode.appendChild(doc.createTextNode(url['lastmod']))
        urlnode.appendChild(lastmodnode)

        changefreqnode = doc.createElement('changefreq')
        changefreqnode.appendChild(doc.createTextNode(url['changefreq']))
        urlnode.appendChild(changefreqnode)

        prioritynode = doc.createElement('priority')
        prioritynode.appendChild(doc.createTextNode(str(url['priority'])))
        urlnode.appendChild(prioritynode)

        urlsetnode.appendChild(urlnode)

    f = open(path, 'w')
    f.write(doc.toprettyxml(indent='  ', encoding='utf-8'))
    f.close()


if __name__ == '__main__':
    build('www.weixinbay.com', './pages/sitemap.xml', 3)
    build('m.weixinbay.com', './m_pages/sitemap.xml', 3)
