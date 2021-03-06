#!/usr/bin/env php
<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*

CAUTION: This file installs the dependencies needed to run the Symfony2 test suite.
If you want to create a new project, download the Symfony Standard Edition instead:

http://symfony.com/download

*/

set_time_limit(0);

if (!is_dir($vendorDir = dirname(__FILE__).'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

$deps = array(
    array('symfony', 'git://github.com/symfony/symfony', '2.0.4'),
    array('doctrine', 'git://github.com/doctrine/doctrine2.git', '2.1.2'),
    array('doctrine-dbal', 'git://github.com/doctrine/dbal.git', '2.1.3'),
    array('doctrine-common', 'git://github.com/doctrine/common.git', '2.1.2'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    echo "> Installing/Updating $name\n";

    $installDir = $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
