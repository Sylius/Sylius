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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion as BaseCatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslation;
use Webmozart\Assert\Assert;

class CatalogPromotion extends BaseCatalogPromotion implements CatalogPromotionInterface
{
    /** @var Collection<array-key, ChannelInterface> */
    protected Collection $channels;

    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
    }

    public function getChannels(): Collection
    {
        /** @phpstan-ignore-next-line */
        return $this->channels;
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        Assert::isInstanceOf($channel, ChannelInterface::class);
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        Assert::isInstanceOf($channel, ChannelInterface::class);
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public static function getTranslationClass(): string
    {
        return CatalogPromotionTranslation::class;
    }
}
