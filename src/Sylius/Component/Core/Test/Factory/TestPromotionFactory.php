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

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TestPromotionFactory implements TestPromotionFactoryInterface
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
    public function create(string $name): PromotionInterface
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionFactory->createNew();

        $promotion->setName($name);
        $promotion->setCode(StringInflector::nameToLowercaseCode($name));
        $promotion->setStartsAt(new \DateTime('-3 days'));
        $promotion->setEndsAt(new \DateTime('+3 days'));

        return $promotion;
    }

    /**
     * {@inheritdoc}
     */
    public function createForChannel(string $name, ChannelInterface $channel): PromotionInterface
    {
        $promotion = $this->create($name);
        $promotion->addChannel($channel);

        return $promotion;
    }
}
