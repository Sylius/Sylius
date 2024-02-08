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

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class AjaxContext implements Context
{
    public function __construct(
        private AbstractBrowser $client,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @When I look for a variant with :phrase in descriptor within the :product product
     */
    public function iLookForVariantWithDescriptorWithinProduct($phrase, ProductInterface $product): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->requestStack->getSession()->getName(), $this->requestStack->getSession()->getId()));
        $this->client->request(
            'GET',
            '/admin/ajax/product-variants/search',
            ['phrase' => $phrase, 'productCode' => $product->getCode()],
            [],
            ['ACCEPT' => 'application/json'],
        );
    }

    /**
     * @Then /^I should see (\d+) product variants? on the list$/
     */
    public function iShouldSeeProductVariantsInTheList($number): void
    {
        Assert::eq(count($this->getJSONResponse()), $number);
    }

    /**
     * @Then I should see the product variant named :firstName on the list
     * @Then I should see the product variants named :firstName and :secondName on the list
     * @Then I should see the product variants named :firstName, :secondName and :thirdName on the list
     * @Then I should see the product variants named :firstName, :secondName, :thirdName and :fourthName on the list
     */
    public function iShouldSeeTheProductVariantNamedAnd(...$names): void
    {
        $itemsNames = array_map(static fn ($item) => strstr($item['descriptor'], ' ', true), $this->getJSONResponse());

        Assert::allOneOf($itemsNames, $names);
    }

    /**
     * @Then I should see the product variant labeled :label on the list
     */
    public function iShouldSeeTheProductVariantLabeledAs($label): void
    {
        $itemsLabels = array_column($this->getJSONResponse(), 'descriptor');

        Assert::oneOf($label, $itemsLabels, 'Expected "%s" to be on the list, found: %s.');
    }

    private function getJSONResponse()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
