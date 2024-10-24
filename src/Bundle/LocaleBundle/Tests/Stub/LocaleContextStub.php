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

namespace Sylius\Bundle\LocaleBundle\Tests\Stub;

use Sylius\Bundle\LocaleBundle\Attribute\AsLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;

#[AsLocaleContext(priority: 15)]
final class LocaleContextStub implements LocaleContextInterface
{
    public function getLocaleCode(): string
    {
        return '';
    }
}
