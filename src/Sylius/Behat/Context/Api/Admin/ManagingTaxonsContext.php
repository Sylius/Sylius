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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    private AbstractBrowser $client;

    private SessionInterface $session;

    private ResponseCheckerInterface $responseChecker;

    public function __construct(AbstractBrowser $client, SessionInterface $session, ResponseCheckerInterface $responseChecker)
    {
        $this->client = $client;
        $this->session = $session;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I look for a taxon with :phrase in name
     */
    public function iTypeIn($phrase): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/search', ['phrase' => $phrase], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When I want to get taxon with :code code
     */
    public function iWantToGetTaxonWithCode($code): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leaf', ['code' => $code], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When /^I want to get children from (taxon "[^"]+")/
     */
    public function iWantToGetChildrenFromTaxon(TaxonInterface $taxon): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leafs', ['parentCode' => $taxon->getCode()], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When I want to get taxon root
     */
    public function iWantToGetTaxonRoot(): void
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/root-nodes', [], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @Then /^I should see (\d+) taxons on the list$/
     */
    public function iShouldSeeTaxonsInTheList($number): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getResponse());

        Assert::eq(count($response), $number);
    }

    /**
     * @Then I should see the taxon named :firstName in the list
     * @Then I should see the taxon named :firstName and :secondName in the list
     * @Then I should see the taxon named :firstName, :secondName and :thirdName in the list
     * @Then I should see the taxon named :firstName, :secondName, :thirdName and :fourthName in the list
     */
    public function iShouldSeeTheTaxonNamedAnd(...$expectedTaxonNames): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getResponse());
        $taxonNames = array_column($response, 'name');

        Assert::allOneOf($expectedTaxonNames, $taxonNames);
    }
}
