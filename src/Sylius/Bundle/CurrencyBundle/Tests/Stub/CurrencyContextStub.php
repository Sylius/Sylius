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

namespace Sylius\Bundle\CurrencyBundle\Tests\Stub;

use Sylius\Bundle\CurrencyBundle\Attribute\AsCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

#[AsCurrencyContext(priority: 15)]
final class CurrencyContextStub implements CurrencyContextInterface
{
    public function getCurrencyCode(): string
    {
        return '';
    }
}
