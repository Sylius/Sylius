<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var RepositoryInterface
     */
    private $actionRepository;

    /**
     * @var TestPromotionFactoryInterface
     */
    private $testPromotionFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PromotionRepositoryInterface $promotionRepository
     * @param RepositoryInterface $actionRepository
     * @param TestPromotionFactoryInterface $testPromotionFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionRepositoryInterface $promotionRepository,
        RepositoryInterface $actionRepository,
        TestPromotionFactoryInterface $testPromotionFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->promotionRepository = $promotionRepository;
        $this->actionRepository = $actionRepository;
        $this->testPromotionFactory = $testPromotionFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is a promotion :promotionName
     */
    public function thereIsPromotion($promotionName)
    {
        $promotion = $this->testPromotionFactory->createPromotion($promotionName);
        $promotion->addChannel($this->sharedStorage->getCurrentResource('channel'));

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->setCurrentResource('promotion', $promotion);
    }

    /**
     * @Given /^it gives "(?:€|£|\$)([^"]+)" fixed discount to every order$/
     */
    public function itGivesFixedDiscountForCustomersWithCartsAbove($amount)
    {
        $currentPromotion = $this->sharedStorage->getCurrentResource('promotion');

        $action = $this->testPromotionFactory->createFixedDiscountAction($amount, $currentPromotion);
        $this->actionRepository->add($action);

        $this->objectManager->flush();
    }
}
