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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ManagingProductTaxonsContext implements Context
{
    private UpdateSimpleProductPageInterface $updateSimpleProductPage;

    public function __construct(UpdateSimpleProductPageInterface $updateSimpleProductPage)
    {
        $this->updateSimpleProductPage = $updateSimpleProductPage;
    }

    /**
     * @When I change that the :product product belongs to the :taxon taxon
     */
    public function iChangeThatTheProductBelongsToTheTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);
        $this->updateSimpleProductPage->selectProductTaxon($taxon);
        $this->updateSimpleProductPage->saveChanges();
    }

    /**
     * @When I change that the :product product does not belong to the :taxon taxon
     */
    public function iChangeThatTheProductDoesNotBelongToTheTaxon(
        ProductInterface $product,
        TaxonInterface $taxon
    ): void {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);
        $this->updateSimpleProductPage->unselectProductTaxon($taxon);
        $this->updateSimpleProductPage->saveChanges();
    }
}
