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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CatalogPromotionContext implements Context
{
    private ExampleFactoryInterface $catalogPromotionExampleFactory;

    private FactoryInterface $catalogPromotionRuleFactory;

    private FactoryInterface $catalogPromotionActionFactory;

    private EntityManagerInterface $entityManager;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ExampleFactoryInterface $catalogPromotionExampleFactory,
        FactoryInterface $catalogPromotionRuleFactory,
        FactoryInterface $catalogPromotionActionFactory,
        EntityManagerInterface $entityManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->catalogPromotionExampleFactory = $catalogPromotionExampleFactory;
        $this->catalogPromotionRuleFactory = $catalogPromotionRuleFactory;
        $this->catalogPromotionActionFactory = $catalogPromotionActionFactory;
        $this->entityManager = $entityManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a catalog promotion with :code code and :name name
     */
    public function thereIsACatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->createCatalogPromotion($name, $code);

        $this->entityManager->flush();
    }

    /**
     * @Given there are catalog promotions named :firstName and :secondName
     */
    public function thereAreCatalogPromotionsNamed(string ...$names): void
    {
        foreach ($names as $name) {
            $this->createCatalogPromotion($name);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given the catalog promotion :catalogPromotion is available in :channel
     */
    public function theCatalogPromotionIsAvailableIn(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel
    ): void {
        $catalogPromotion->addChannel($channel);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) will be applied on ("[^"]+" variant)$/
     */
    public function itWillBeAppliedOnVariant(CatalogPromotionInterface $catalogPromotion, ProductVariantInterface $variant): void
    {
        /** @var CatalogPromotionRuleInterface $catalogPromotionRule */
        $catalogPromotionRule = $this->catalogPromotionRuleFactory->createNew();
        $catalogPromotionRule->setType(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $catalogPromotionRule->setConfiguration(['variants' => [$variant->getCode()]]);

        $catalogPromotion->addRule($catalogPromotionRule);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) will reduce price by ("[^"]+")$/
     */
    public function itWillReducePrice(CatalogPromotionInterface $catalogPromotion, float $discount): void
    {
        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $catalogPromotionAction->setConfiguration(['amount' => $discount]);

        $catalogPromotion->addAction($catalogPromotionAction);

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion with "([^"]*)" name that applies on ("[^"]+" variant) and reduces price by ("[^"]+")$/
     */
    public function thereIsACatalogPromotionWithNameThatAppliesOnVariantAndReducesPriceBy(
        string $name,
        ProductVariantInterface $variant,
        float $discount
    ): void {
        $catalogPromotion = $this->createCatalogPromotion($name);

        /** @var CatalogPromotionRuleInterface $catalogPromotionRule */
        $catalogPromotionRule = $this->catalogPromotionRuleFactory->createNew();
        $catalogPromotionRule->setType(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $catalogPromotionRule->setConfiguration(['variants' => [$variant->getCode()]]);

        $catalogPromotion->addRule($catalogPromotionRule);

        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $catalogPromotionAction->setConfiguration(['amount' => $discount]);

        $catalogPromotion->addAction($catalogPromotionAction);

        $this->entityManager->flush();
    }

    private function createCatalogPromotion(string $name, ?string $code = null): CatalogPromotionInterface
    {
        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionExampleFactory->create([
            'name' => $name,
            'code' => $code,
        ]);

        $this->entityManager->persist($catalogPromotion);
        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);

        return $catalogPromotion;
    }
}
