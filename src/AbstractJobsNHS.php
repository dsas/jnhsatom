<?php

namespace dsas\jnhsatom;

use Zend\Feed\Writer\Feed;
use Zend\Feed\Writer\Entry;
use PHPHtmlParser\Dom;

abstract class AbstractJobsNHS
{
    /**
     * Protocol & domain
     * @var string
     */
    const BASE_URL = 'http://www.jobs.nhs.uk';

    /**
     * URL for the news page relative to BASE_URL
     * @return string
     */
    abstract protected function getNewsPage();

    /**
     * Add created/modified time to an entry given the news page
     */
    abstract protected function addArticleTimeToEntry(Entry $entry, Dom $article);

    /**
     * Adds entries to the feed
     */
    public function addEntriesToFeed(Feed $feed)
    {
        $indexDom = $this->getPage(self::BASE_URL . $this->getNewsPage());
        $article_links = $indexDom->find('.articleItem h2 a');

        foreach ($article_links as $article_link) {
            $article_url = $this->canonicaliseNewItemLinks($article_link);
            $article = $this->getPage($article_url);

            $entry = $feed->createEntry();

            $article_title = $article->find('h2', 0);
            $entry->setTitle(html_entity_decode($article_title->text()));
            $entry->setLink(html_entity_decode($article_url));
            $entry->setContent(html_entity_decode($article->find('.articleContent', 0)->innerHTML()));

            $this->addArticleTimeToEntry($entry, $article);

            $feed->addEntry($entry);
        }
    }

    /**
     * Given a url, get the Dom object representing it
     * @return Dom
     */
    private function getPage($Url)
    {
        $page = new Dom();
        $page->loadFromUrl($Url);
        return $page;
    }

    /**
     * Remove the odd hex string in URLs, it doesn't appear to be useful
     */
    private function canonicaliseNewItemLinks($url)
    {
        $url = self::BASE_URL . $url->getAttribute('href');
        $news_page = preg_quote($this->getNewsPage());
        // There's some hex string, presumably a session id in the links,
        // get rid of it, otherwise the ID will change all of the time.
        $url = preg_replace('|(.*'.$news_page.')\/[a-z0-9]*(\/\?news_id=[0-9]*)|', '$1$2', $url);
        return $url;
    }
}
