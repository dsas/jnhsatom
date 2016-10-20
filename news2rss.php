<?php

require_once 'vendor/autoload.php';

use PHPHtmlParser\Dom;
use Zend\Feed\Writer\Feed;

define('BASE_URL', 'http://www.jobs.nhs.uk');
define('NEWS_URL', BASE_URL . '/xi/js_news/');

$page = new Dom();
$page->loadFromUrl(NEWS_URL);

$feed = new Feed();
$feed->setTitle(html_entity_decode($page->find('title', 0)->text()));
$feed->setLink(NEWS_URL);
$feed->setFeedLink('http://bits.deansas.org/jnhs.atom', 'atom');
$feed->setCopyright('NHS Jobs');

$article_links = $page->find('.articleItem h2 a');
foreach ($article_links as $article_link) {
    $article_url = BASE_URL . $article_link->getAttribute('href');
    // There's some hex string, presumably a session id in the links,
    // get rid of it, otherwise the ID will change all of the time.
    $article_url = preg_replace('/(.*js_news)\/[a-z0-9]*(\/\?news_id=[0-9]*)/', '$1$2', $article_url);

    $article = new Dom();
    $article->loadFromUrl($article_url);

    $entry = $feed->createEntry();

    $article_title = $article->find('h2', 0);
    $entry->setTitle(html_entity_decode($article_title->text()));
    $entry->setLink(html_entity_decode($article_url));
    $entry->setContent(html_entity_decode($article->find('.articleContent', 0)->innerHTML()));
    $time_meta = $article->find('.posted', 0)->getChildren();

    $entry->setDateModified(strtotime($time_meta[3]));
    $entry->setDateCreated(strtotime($time_meta[1]));

    $feed->addEntry($entry);

    if (!$feed->getDateModified()) {
        $feed->setDateModified(strtotime($time_meta[3]));
    }
}

print $feed->export('atom');
