<?php
// Allows for an API call to get all public tags for a given url.

//Send as json
$httpContentType = 'application/json';

// Do NOT force auth.
require_once dirname(__DIR__) . '/www-header.php';

//Get the url to query
$url = "";
if (isset($_GET['url'])) {
    $url = md5(urldecode($_GET['url']));
}

/* Service creation: only useful services are created */
$bookmarkService = SemanticScuttle_Service_Factory::get('Bookmark');
$b2tservice      = SemanticScuttle_Service_Factory::get('Bookmark2Tag');

//Create an array of tags
$tags = array();

//Fail early if no url was given.  (if empty url, tags may still be returned?).
if (empty($url)) {
    echo json_encode($tags);
    exit();
}

//Get all bookmarks from everyone for the given url.
$bookmarks = $bookmarkService->getBookmarks(0, null, null, null, null, null, null, null, null, $url);

//look though bookmarks to get tags...
foreach ($bookmarks['bookmarks'] as $bookmark) {
    //Do not get tags for this bookmark if it is not public (public = 0, limited=1, private=2)
    if ($bookmark['bStatus'] != 0) {
        continue;
    }
    
    //actually get the tags
    $tags += $b2tservice->getTagsForBookmark($bookmark['bId'], true);
}

//Echo a json encoded array of unique tags.
echo json_encode(array_unique($tags));