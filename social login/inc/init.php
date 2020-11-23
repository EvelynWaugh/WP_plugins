<?php
myOwnListing()->boot([
    Evelyn\Src\Admin\Admin::class,
    \Evelyn\Ext\Social_Login\Social_Login::class
]);
myOwnListing()->register([
    'helpers' => Evelyn\Utils\Helpers::instance()
]);
