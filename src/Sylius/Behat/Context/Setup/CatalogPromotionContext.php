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
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CatalogPromotionContext implements Context
{
    private FactoryInterface $catalogPromotionFactory;

    private EntityManagerInterface $entityManager;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        FactoryInterface $catalogPromotionFactory,
        EntityManagerInterface $entityManager,
        SharedStorageInterface $sharedStorage
    ) {
        $this->catalogPromotionFactory = $catalogPromotionFactory;
        $this->entityManager = $entityManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a catalog promotion with :code code and :name name
     */
    public function thereIsACatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionFactory->createNew();
        $catalogPromotion->setName($name);
        $catalogPromotion->setCode($code);

        $this->entityManager->persist($catalogPromotion);
        $this->entityManager->flush();

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @Given /^(it) will be applied on ("[^"]+" taxon)$/
     */
    public function itWillBeAppliedOnTaxon(CatalogPromotionInterface $catalogPromotion, TaxonInterface $taxon): void
    {
        // TODO: Add Taxon Rule to the Catalog Promotion
    }

    /**
     * @Given /^(it) will reduce price by ([^"]+)%$/
     */
    public function itWillReducePrice(CatalogPromotionInterface $catalogPromotion, int $discount): void
    {
        // TODO: Add fixed discount Action to the Catalog Promotion
    }
}
