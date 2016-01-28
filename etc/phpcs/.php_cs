<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\CS\FixerInterface;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the Sylius package.

(c) Paweł Jędrzejewski

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

$finder = Symfony\Component\Finder\Finder::create()
    ->files()
    ->notName('*Spec.php')
    ->name('*.php')
    ->in(['app/migrations','src'])
;

return Symfony\CS\Config\Config::create()
        ->setUsingCache(true)
        ->fixers(array('header_comment'))
        ->finder($finder)
;
