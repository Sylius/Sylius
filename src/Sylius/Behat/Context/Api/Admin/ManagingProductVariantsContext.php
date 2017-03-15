<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingProductVariantsContext implements Context
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param Client $client
     * @param SessionInterface $session
     */
    public function __construct(Client $client, SessionInterface $session)
    {
        $this->client = $client;
        $this->session = $session;
    }

    /**
     * @When I look for a variant with :phrase in descriptor within the :product product
     */
    public function iLookForVariantWithDescriptorWithinProduct($phrase, ProductInterface $product)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request(
            'GET',
            '/admin/ajax/product-variants/search',
            ['phrase' => $phrase, 'productCode' => $product->getCode()],
            [],
            ['ACCEPT' => 'application/json']
        );
    }

    /**
     * @Then /^I should see (\d+) product variants? on the list$/
     */
    public function iShouldSeeProductVariantsInTheList($number)
    {
        Assert::eq(count($this->getJSONResponse()), $number);
    }

    /**
     * @Then I should see the product variant named :firstName on the list
     * @Then I should see the product variants named :firstName and :secondName on the list
     * @Then I should see the product variants named :firstName, :secondName and :thirdName on the list
     * @Then I should see the product variants named :firstName, :secondName, :thirdName and :fourthName on the list
     */
    public function iShouldSeeTheProductVariantNamedAnd(...$names)
    {
        $itemsNames = array_map(function ($item) {
            return strstr($item['descriptor'], ' ', true);
        }, $this->getJSONResponse());

        Assert::allOneOf($itemsNames, $names);
    }

    /**
     * @Then I should see the product variant labeled :label on the list
     */
    public function iShouldSeeTheProductVariantLabeledAs($label)
    {
        $itemsLabels = array_map(function ($item) {
            return $item['descriptor'];
        }, $this->getJSONResponse());

        Assert::oneOf($label, $itemsLabels, 'Expected "%s" to be on the list, found: %s.');
    }

    /**
     * @return mixed
     */
    private function getJSONResponse()
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
