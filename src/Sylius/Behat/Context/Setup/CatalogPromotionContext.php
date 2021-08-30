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
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRule;

final class CatalogPromotionContext implements Context
{
    private ExampleFactoryInterface $catalogPromotionExampleFactory;

    private EntityManagerInterface $entityManager;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ExampleFactoryInterface $catalogPromotionExampleFactory,
        EntityManagerInterface $entityManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->catalogPromotionExampleFactory = $catalogPromotionExampleFactory;
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
        $catalogPromotionRule = new CatalogPromotionRule();

        $catalogPromotionRule->setType(CatalogPromotionRule::TYPE_CONTAINS_VARIANTS);
        $catalogPromotionRule->setConfiguration([$variant->getCode()]);

        $catalogPromotion->addRule($catalogPromotionRule);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) will reduce price by ([^"]+)%$/
     */
    public function itWillReducePrice(CatalogPromotionInterface $catalogPromotion, int $discount): void
    {
        // TODO: Add fixed discount Action to the Catalog Promotion
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
