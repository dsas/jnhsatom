<?php

require_once 'vendor/autoload.php';

use Zend\Feed\Writer\Feed;

$feed = new Feed();
$feed->setId('https://bits.deansas.org/jnhs.atom');
$feed->setTitle("NHS Jobs news");
$feed->setFeedLink('http://bits.deansas.org/jnhs.atom', 'atom');
$feed->setCopyright('NHS Jobs');
$feed->addAuthor([
    'name' => 'job.nhs.uk',
]);

foreach (['dsas\jnhsatom\JobsNHSUsers', 'dsas\jnhsatom\JobsNHSEmployers'] as $entrySourceClass) {
    $entrySource = new $entrySourceClass();
    $entrySource->addEntriesToFeed($feed);
}

if (count($feed)) {
    $feed->orderByDate();
    $feed->setDateModified($feed->getEntry()->getDateModified());
}

print $feed->export('atom');
