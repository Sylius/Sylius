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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductVariantTranslationInterface extends ResourceInterface, TranslationInterface
{
    /**
     * @return string
     */
    public function getName(): ?string;

    public function setName(?string $name): void;
}
