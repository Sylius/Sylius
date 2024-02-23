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

namespace Sylius\Behat\Service\Converter;

interface SectionAwareIriConverterInterface
{
    public function getIriFromResourceInSection(object $item, string $section, ?int $referenceType = null): string;
}
