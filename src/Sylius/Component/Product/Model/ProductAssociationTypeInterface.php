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

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductAssociationTypeInterface extends
    CodeAwareInterface,
    TimestampableInterface,
    ResourceInterface,
    TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    /**
     * @return ProductAssociationTypeTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
