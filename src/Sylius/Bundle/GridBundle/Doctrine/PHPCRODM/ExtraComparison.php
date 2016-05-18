<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

/**
 * Contains constants values for comparisons which are not supported
 * by the Doctrine\Common\Collection\Expr\Comparison class.
 */
class ExtraComparison
{
    const NOT_CONTAINS = 'NOT_CONTAINS';
    const IS_NULL = 'IS_NULL';
    const IS_NOT_NULL = 'IS_NOT_NULL';
}
