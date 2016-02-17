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

use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TestPromotionFactory implements TestPromotionFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $actionFactory;

    /**
     * @var FactoryInterface
     */
    private $promotionFactory;

    /**
     * @param FactoryInterface $actionFactory
     * @param FactoryInterface $promotionFactory
     */
    public function __construct(
        FactoryInterface $actionFactory,
        FactoryInterface $promotionFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->promotionFactory = $promotionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createPromotion($name)
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
    public function createFixedDiscountAction($discount, PromotionInterface $promotion)
    {
        $action = $this->actionFactory->createNew();

        $action->setType(ActionInterface::TYPE_FIXED_DISCOUNT);
        $action->setConfiguration(['amount' => $this->getPriceFromString($discount)]);
        $action->setPromotion($promotion);

        return $action;
    }

    /**
     * @param string $promotionName
     *
     * @return string
     */
    private function getCodeFromName($promotionName)
    {
        return str_replace(' ', '_', strtolower($promotionName));
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round(($price * 100), 2);
    }
}
