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
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion as BaseCatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslation;

class CatalogPromotion extends BaseCatalogPromotion implements CatalogPromotionInterface
{
    /**
     * @var Collection<array-key, ChannelInterface>
     *
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected Collection $channels;

    /**
     * @var Collection<array-key, ChannelPricingInterface>
     *
     * @psalm-var Collection<array-key, ChannelPricingInterface>
     */
    protected Collection $channelPricings;


    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
        $this->channelPricings = new ArrayCollection();
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function getChannelPricings(): Collection
    {
        return $this->channelPricings;
    }

    public function hasChannelPricing(ChannelPricingInterface $channelPricing): bool
    {
        return $this->channelPricings->contains($channelPricing);
    }

    public function addChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if (!$this->hasChannelPricing($channelPricing)) {
            $this->channelPricings->add($channelPricing);
        }
    }

    public function removeChannelPricing(ChannelPricingInterface $channelPricing): void
    {
        if ($this->hasChannelPricing($channelPricing)) {
            $this->channelPricings->removeElement($channelPricing);
        }
    }

    public static function getTranslationClass(): string
    {
        return CatalogPromotionTranslation::class;
    }
}
