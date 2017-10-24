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

namespace Sylius\Component\Core\Model;

use Sylius\Component\Product\Model\ProductTranslation as BaseProductTranslation;

class ProductTranslation extends BaseProductTranslation implements ProductTranslationInterface
{
    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * {@inheritdoc}
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription(?string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }
}
