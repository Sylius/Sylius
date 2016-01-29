<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$header = <<<EOF
This file is part of the Sylius package.

(c) Paweł Jędrzejewski

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

return [
    'header_comment',
    'short_array_syntax',
    'ordered_use',
    '-empty_return',
    '-phpdoc_no_empty_return',
    '-phpdoc_params',
    '-phpdoc_short_description',
];
