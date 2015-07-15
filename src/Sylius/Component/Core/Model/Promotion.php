<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Promotion\Model\Promotion as BasePromotion;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;

/**
 * Promotion model.
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class Promotion extends BasePromotion implements PromotionInterface
{
    /**
     * Channels in which this product is available.
     *
     * @var ChannelInterface[]|Collection
     */
    protected $channels;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->channels = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannels(Collection $channels)
    {
        $this->channels = $channels;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(BaseChannelInterface $channel)
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(BaseChannelInterface $channel)
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(BaseChannelInterface $channel)
    {
        return $this->channels->contains($channel);
    }
}
