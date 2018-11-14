<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.3. Doctrine MongoDB and PHPCR support will no longer be supported in Sylius 2.0.', ExtraComparison::class), \E_USER_DEPRECATED);

/**
 * Contains constants values for comparisons which are not supported
 * by the Doctrine\Common\Collection\Expr\Comparison class.
 */
final class ExtraComparison
{
    public const NOT_CONTAINS = 'NOT_CONTAINS';
    public const IS_NULL = 'IS_NULL';
    public const IS_NOT_NULL = 'IS_NOT_NULL';

    private function __construct()
    {
    }
}
