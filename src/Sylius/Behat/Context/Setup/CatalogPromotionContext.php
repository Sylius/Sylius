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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class CatalogPromotionContext implements Context
{
    /**
     * @Given there is a catalog promotions with :code code and :name name
     */
    public function thereIsACatalogPromotionsWithCodeAndName(string $code, string $name): void
    {
        throw new PendingException();
    }

    /**
     * @Given /^(it) will be applied on ("[^"]+" taxon)$/
     */
    public function itWillBeAppliedOnTaxon(CatalogPromotionInterface $catalogPromotion, TaxonInterface $taxon): void
    {
        throw new PendingException();
    }

    /**
     * @Given /^(it) will reduce price by ([^"]+)%$/
     */
    public function itWillReducePriceBy50(CatalogPromotionInterface $catalogPromotion, int $discount): void
    {
        throw new PendingException();
    }
}
