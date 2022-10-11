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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ManagingProductTaxonsContext implements Context
{
    public function __construct(private ApiClientInterface $client, private IriConverterInterface $iriConverter)
    {
    }

    /**
     * @When I change that the :product product belongs to the :taxon taxon
     */
    public function iChangeThatTheProductBelongsToTheTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_TAXONS, (string) $product->getProductTaxons()->current()->getId());
        $this->client->updateRequestData(['taxon' => $this->iriConverter->getIriFromItem($taxon)]);
        $this->client->update();
    }

    /**
     * @When I add :taxon taxon to the :product product
     */
    public function iAddTaxonToTheProduct(TaxonInterface $taxon, ProductInterface $product): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('taxon', $this->iriConverter->getIriFromItem($taxon));
        $this->client->addRequestData('product', $this->iriConverter->getIriFromItem($product));
        $this->client->create();
    }
}
