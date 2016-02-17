<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TestPromotionFactory implements TestPromotionFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $promotionFactory;

    /**
     * @param FactoryInterface $promotionFactory
     */
    public function __construct(FactoryInterface $promotionFactory)
    {
        $this->promotionFactory = $promotionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $promotion = $this->promotionFactory->createNew();

        $promotion->setName($name);
        $promotion->setCode($this->getCodeFromName($name));
        $promotion->setDescription('Promotion '.$name);
        $promotion->setStartsAt(new \DateTime('-3 days'));
        $promotion->setEndsAt(new \DateTime('+3 days'));

        return $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function createForChannel($name, ChannelInterface $channel)
    {
        $promotion = $this->create($name);
        $promotion->addChannel($channel);

        return $promotion;
    }
}
