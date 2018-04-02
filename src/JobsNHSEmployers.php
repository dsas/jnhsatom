<?php

namespace dsas\jnhsatom;

use Zend\Feed\Writer\Entry;
use PHPHtmlParser\Dom;

/**
 * Entries for the employer news on jobs.nhs.uk
 */
class JobsNHSEmployers extends AbstractJobsNHS
{
    protected function getNewsPage()
    {
        return '/xi/emp_news';
    }

    protected function addArticleTimeToEntry(Entry $entry, Dom $article)
    {
        $time_meta = $article->find('.notPara', 0)->getChildren();
        $entry->setDateModified(strtotime($time_meta[3]));
        $entry->setDateCreated(strtotime($time_meta[1]));
    }
}
