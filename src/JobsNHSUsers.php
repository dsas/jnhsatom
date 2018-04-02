<?php

namespace dsas\jnhsatom;

use Zend\Feed\Writer\Entry;
use PHPHtmlParser\Dom;

/**
 * Entries for the users (applicants) news on jobs.nhs.uk
 */
class JobsNHSUsers extends AbstractJobsNHS
{
    protected function getNewsPage()
    {
        return '/xi/js_news';
    }

    protected function addArticleTimeToEntry(Entry $entry, Dom $article)
    {
        $time_meta = $article->find('.posted', 0)->getChildren();
        $entry->setDateModified(strtotime($time_meta[3]));
        $entry->setDateCreated(strtotime($time_meta[1]));
    }
}
