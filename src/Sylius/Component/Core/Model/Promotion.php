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
use Sylius\Component\Promotion\Model\Promotion as BasePromotion;
use Webmozart\Assert\Assert;

class Promotion extends BasePromotion implements PromotionInterface
{
    /**
     * @var Collection|ChannelInterface[]
     *
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected $channels;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, ChannelInterface> $this->channels */
        $this->channels = new ArrayCollection();
    }

    /**
     * @psalm-suppress InvalidReturnType https://github.com/doctrine/collections/pull/220
     * @psalm-suppress InvalidReturnStatement https://github.com/doctrine/collections/pull/220
     */
    public function getChannels(): Collection
    {
        /** @phpstan-ignore-next-line */
        return $this->channels;
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

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }
}
