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

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('*.yml')
    ->notName('*.xml')
    ->notName('*Spec.php')
    ->exclude('app')
    ->fixers(array('header_comment'))
;

return Symfony\CS\Config\Config::create()->finder($finder);
