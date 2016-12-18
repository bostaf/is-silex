<?php

namespace Is\Service;

class News
{
    private $news;

    public function __construct() {}

    public function getNews() {

        $dir_handle = opendir('./data/news');

        return array();
    }
}