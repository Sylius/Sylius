<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Exception;

final class CannotRemoveMenuTaxonException extends \RuntimeException
{
    public function __construct(string $code)
    {
        parent::__construct(sprintf('You cannot delete a menu taxon with code "%s" of any channel.', $code));
    }
}
