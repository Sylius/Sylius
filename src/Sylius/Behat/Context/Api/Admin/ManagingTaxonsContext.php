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
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingTaxonsContext implements Context
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
     * @When I look for a taxon with :phrase in name
     */
    public function iTypeIn($phrase)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/search', ['phrase' => $phrase], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When I want to get taxon with :code code
     */
    public function iWantToGetTaxonWithCode($code)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leaf', ['code' => $code], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When /^I want to get children from (taxon "[^"]+")/
     */
    public function iWantToGetChildrenFromTaxon(TaxonInterface $taxon)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leafs', ['parentCode' => $taxon->getCode()], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When I want to get taxon root
     */
    public function iWantToGetTaxonRoot()
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/root-nodes', [], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @Then /^I should see (\d+) taxons on the list$/
     */
    public function iShouldSeeTaxonsInTheList($number)
    {
        $response = json_decode($this->client->getResponse()->getContent(), true);

        Assert::eq(count($response), $number);
    }

    /**
     * @Then I should see the taxon named :firstName in the list
     * @Then I should see the taxon named :firstName and :secondName in the list
     * @Then I should see the taxon named :firstName, :secondName and :thirdName in the list
     * @Then I should see the taxon named :firstName, :secondName, :thirdName and :fourthName in the list
     */
    public function iShouldSeeTheTaxonNamedAnd(...$expectedTaxonNames)
    {
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $taxonNames = array_map(function ($item) {
            return $item['name'];
        }, $response);

        Assert::allOneOf($taxonNames, $expectedTaxonNames);
    }
}
