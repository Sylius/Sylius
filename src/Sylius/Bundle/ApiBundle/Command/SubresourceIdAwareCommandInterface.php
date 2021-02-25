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

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
interface SubresourceIdAwareCommandInterface extends EnrichableCommandInterface
{
    public function getSubresourceId(): ?string;

    public function setSubresourceId(?string $subresourceId): void;

    public function getSubresourceIdAttributeKey(): string;
}
