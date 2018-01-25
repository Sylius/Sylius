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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Shipping\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
{
    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * @var Collection
     */
    protected $channels;

    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getZone(): ?ZoneInterface
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(?ZoneInterface $zone): void
    {
        $this->zone = $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory(): ?TaxCategoryInterface
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(?TaxCategoryInterface $category): void
    {
        $this->taxCategory = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass(): string
    {
        return ShippingMethodTranslation::class;
    }
}
