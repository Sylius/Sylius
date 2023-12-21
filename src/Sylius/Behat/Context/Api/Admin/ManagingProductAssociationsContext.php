<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Repository\ProductAssociationRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

class ManagingProductAssociationsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private ProductAssociationRepositoryInterface $associationRepository,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When /^I (associate as "[^"]+") the (product "[^"]+") with the ("[^"]+" product)$/
     */
    public function iAssociateAsTypeTheProductWithTheProduct(
        ProductAssociationTypeInterface $type,
        ProductInterface $owner,
        ProductInterface $product,
    ): void {
        $this->iAssociateAsTypeTheProductWithTheProducts($type, $owner, [$product]);
    }

    /**
     * @When /^I (associate as "[^"]+") the (product "[^"]+") with the (products "[^"]+" and "[^"]+")$/
     */
    public function iAssociateAsTypeTheProductWithTheProducts(
        ProductAssociationTypeInterface $type,
        ProductInterface $owner,
        array $products,
    ): void {
        $associatedProductsData = [];
        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $associatedProductsData[] = $this->iriConverter->getIriFromItem($product);
        }

        $this->client->buildCreateRequest(Resources::PRODUCT_ASSOCIATIONS);
        $this->client->addRequestData('type', $this->iriConverter->getIriFromItem($type));
        $this->client->addRequestData('owner', $this->iriConverter->getIriFromItem($owner));
        $this->client->addRequestData('associatedProducts', $associatedProductsData);
        $this->client->create();

        /** @var ProductAssociationInterface $association */
        $association = $this->associationRepository->findOneBy([
            'owner' => $owner,
            'type' => $type,
        ]);

        $this->sharedStorage->set('association', $association);
        $this->sharedStorage->set('product', $association->getOwner());
    }

    /**
     * @When /^I add the (product "[^"]+") to (this product association)$/
     */
    public function iAddTheProductToThisProductAssociation(
        ProductInterface $product,
        ProductAssociationInterface $association,
    ): void {
        $this->client->buildUpdateRequest(Resources::PRODUCT_ASSOCIATIONS, (string) $association->getId());

        $associatedProducts = [$this->iriConverter->getIriFromItem($product)];
        foreach ($association->getAssociatedProducts() as $associatedProduct) {
            $associatedProducts[] = $this->iriConverter->getIriFromItem($associatedProduct);
        }

        $this->client->setRequestData(['associatedProducts' => $associatedProducts]);
        $this->client->update();

        $this->sharedStorage->set('association', $association);
    }

    /**
     * @When /^I change (this product association)'s product to the ("[^"]+" product)$/
     */
    public function iChangeThisProductAssociationProductToProduct(
        ProductAssociationInterface $association,
        ProductInterface $product,
    ): void {
        $this->client->buildUpdateRequest(Resources::PRODUCT_ASSOCIATIONS, (string) $association->getId());
        $this->client->addRequestData('associatedProducts', [$this->iriConverter->getIriFromItem($product)]);
        $this->client->update();

        $this->sharedStorage->set('association', $association);
    }

    /**
     * @When /^I remove the (product "[^"]+") from (this product association)$/
     */
    public function iRemoveTheProductFromThisProductAssociation(
        ProductInterface $product,
        ProductAssociationInterface $association,
    ): void {
        $this->client->buildUpdateRequest(Resources::PRODUCT_ASSOCIATIONS, (string) $association->getId());

        $associatedProducts = [];
        foreach ($association->getAssociatedProducts() as $associatedProduct) {
            if ($associatedProduct->getCode() !== $product->getCode()) {
                $associatedProducts[] = $this->iriConverter->getIriFromItem($associatedProduct);
            }
        }

        $this->client->setRequestData(['associatedProducts' => $associatedProducts]);
        $this->client->update();

        $this->sharedStorage->set('association', $association);
    }

    /**
     * @Then /^(this product) should have an (association "[^"]+")$/
     */
    public function thisProductShouldHaveAnAssociation(
        ProductInterface $product,
        ProductAssociationTypeInterface $type,
    ): void {
        $response = $this->client->show(Resources::PRODUCTS, $product->getCode());
        $associations = $this->responseChecker->getValue($response, 'associations');

        $associationTypeIri = $this->sectionAwareIriConverter->getIriFromResourceInSection($type, 'admin');

        foreach ($associations as $associationIri) {
            $response = $this->client->showByIri($associationIri);
            $productAssociationType = $this->responseChecker->getValue($response, 'type');

            if ($associationTypeIri === $productAssociationType) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Product %s does not have an association of type %s',
            $product->getCode(),
            $type->getName(),
        ));
    }

    /**
     * @Then /^(this association) should only have (product "[^"]+")$/
     */
    public function thisAssociationShouldOnlyHaveProduct(
        ProductAssociationInterface $association,
        ProductInterface $product,
    ): void {
        $this->thisAssociationShouldHaveProducts($association, [$product]);
    }

    /**
     * @Then /^(this association) should have (products "[^"]+" and "[^"]+")$/
     */
    public function thisAssociationShouldHaveProducts(
        ProductAssociationInterface $association,
        array $products,
    ): void {
        $response = $this->client->show(Resources::PRODUCT_ASSOCIATIONS, (string) $association->getId());

        $content = $this->responseChecker->getResponseContent($response);
        $associatedProducts = $content['associatedProducts'];

        Assert::count($associatedProducts, count($products));

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            Assert::inArray(
                $this->sectionAwareIriConverter->getIriFromResourceInSection($product, 'admin'),
                $associatedProducts,
            );
        }
    }
}
