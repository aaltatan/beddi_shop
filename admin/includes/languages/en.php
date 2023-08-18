<?php

function lang($phrase)
{
    static $languages = [
        // aside
        'TITLE' => "Beddi Shop",

        'HOME' => "Dashboard",
        'CATAGORIES' => "Catagories",
        'ITEMS' => "Items",
        'MEMBERS' => "Members",
        'STATISTICS' => "Statistics",
        'LOGS' => "Logs",

        'EDITPROFILE' => "Edit Profile",
        'SETTINGS' => "Settings",
        'LOGOUT' => "Log out",
        // settings
    ];
    return $languages[$phrase];
};
