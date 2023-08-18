<?php

function pageTitle()
{
    global $page_title;
    echo isset($page_title) ? $page_title : "Default";
}
