<?php

function myOwnListing()
{
    return Evelyn\App::instance();
}
function wsjr()
{
    return myOwnListing()->helpers();
}
myOwnListing();
