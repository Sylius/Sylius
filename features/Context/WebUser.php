<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use FOS\RestBundle\Util\Pluralization;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Web user context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class WebUser extends RawMinkContext implements KernelAwareInterface
{
    /**
     * Repository services map.
     *
     * @var array
     */
    protected $repositories = array(
        'tax_category' => 'sylius_taxation.repository.category',
        'tax_rate'     => 'sylius_taxation.repository.rate',
    );

    /**
     * Actions to route parts map.
     *
     * @var array
     */
    protected $actions = array(
        'creation' => 'create',
        'editing'  => 'update',
    );

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^I am on the (.+) page?$/
     * @Given /^I am on the (.+) page in (\w+)$/
     * @When /^I go to the (.+) page?$/
     */
    public function iAmOnThePage($page, $language = null)
    {
        $parameters = array();

        if ($language) {
            $parameters['_locale'] = $this->getLanguageLocale($language);
        }

        $this->getSession()->visit($this->generatePageUrl($page, $parameters));
    }

    /**
     * @Then /^I should be on the (.+) page$/
     * @Then /^I should be on the (.+) page in (\w+)$/
     * @Then /^I should be redirected to the (.+) page$/
     * @Then /^I should still be on the (.+) page$/
     */
    public function iShouldBeOnThePage($page, $language = null)
    {
        $parameters = array();

        if ($language) {
            $parameters['_locale'] = $this->getLanguageLocale($language);
        }

        $this->assertSession()->addressEquals($this->generatePageUrl($page, $parameters));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Given /^I am on the page of ([^""]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePage($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->findOneByName($type, $name);

        $this->getSession()->visit($this->generatePageUrl(sprintf('sylius_backend_%s_show', $type), array('id' => $resource->getId())));
    }

    /**
     * @Then /^I should be on the page of ([^""]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePage($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->findOneByName($type, $name);

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_show', $type), array('id' => $resource->getId())));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I should see "([^"]*)"$/
     * @Then /^(?:.* )?"([^"]*)" should appear on the page$/
     */
    public function iShouldSeeText($text)
    {
        $this->assertSession()->pageTextContains($text);
    }

    /**
     * @When /^I follow "([^"]+)"$/
     */
    public function iFollow($link)
    {
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * @When /^I fill in "([^"]*)" with "([^"]*)"/
     */
    public function iFillInFieldWith($field, $value)
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @When /^I select "([^"]*)" from "([^"]*)"$/
     */
    public function iSelectOptionFrom($option, $field)
    {
        $this->getSession()->getPage()->selectFieldOption($field, $option);
    }

    /**
     * @Then /^I should see "([^"]*)" field error$/
     */
    public function iShouldSeeFieldError($field)
    {
        $this->assertSession()->elementExists('xpath', sprintf(
            "//div[contains(@class, 'error')]//label[text()[contains(., '%s')]]", ucfirst($field)
        ));
    }

    /**
     * @Given /^I leave "([^"]*)" empty$/
     */
    public function iLeaveFieldEmpty($field)
    {
        $this->assertSession()->fieldValueEquals($field, '');
    }

    /**
     * @Given /^I press "([^"]*)"$/
     */
    public function iPress($button)
    {
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * @Given /^I am not authenticated$/
     * @Given /^I am not logged in anymore$/
     */
    public function iAmNotAuthenticated()
    {
        $this->getSecurityContext()->setToken(null);
        $this->getContainer()->get('session')->invalidate();
    }

    /**
     * @Given /^I am logged in as administrator$/
     */
    public function iAmLoggedInAsAdministrator()
    {
        // No security for now.
    }

    /**
     * @Then /^I should be logged in$/
     */
    public function iShouldBeLoggedIn()
    {
        if (!$this->getSecurityContext()->isGranted('ROLE_USER')) {
            throw new AuthenticationException('User is not authenticated.');
        }
    }

    /**
     * @Then /^I should not be logged in$/
     */
    public function iShouldNotBeLoggedIn()
    {
        if ($this->getSecurityContext()->isGranted('ROLE_USER')) {
            throw new AuthenticationException('User was not expected to be logged in, but he is.');
        }
    }

    /**
     * @When /^I go to the website root$/
     */
    public function iGoToTheWebsiteRoot()
    {
        $this->getSession()->visit('/');
    }

    /**
     * For example: I should see product with name "Wine X" in that list.
     *
     * @Then /^I should see ([\w\s]+) with ([\w\s]+) "([^""]*)" in (that|the) list$/
     */
    public function iShouldSeeItemWithValueInThatList($item, $property, $value)
    {
        $values = $this->getActualValuesInTableByColumnName($item, $property);

        assertContains($value, $values);
    }

    /**
     * For example: I should not see product with name "Wine X" in that list.
     *
     * @Then /^I should not see ([\w\s]+) with ([\w\s]+) "([^""]*)" in (that|the) list$/
     */
    public function iShouldNotSeeItemWithValueInThatList($item, $property, $value)
    {
        $values = $this->getActualValuesInTableByColumnName($item, $property);

        assertNotContains($value, $values);
    }

    /**
     * For example: I should see 10 products in that list.
     *
     * @Then /^I should see (\d+) ([^""]*) in (that|the) list$/
     */
    public function iShouldSeeThatMuchItemsInThatList($amount, $item)
    {
        $actual = $this->getCountOfItemsByName($item);

        assertEquals($amount, $actual, sprintf('Failed asserting there are %d %s in the list, in reality they are "%s"', $amount, $item, $actual));
    }

    /**
     * For example: I see product sorted in ascending order by name.
     *
     * @Then /^I see ([^"]*) sorted in (ascending|descending) order by "([^"]*)"$/
     */
    public function iSeeItemsSortedInAscendingOrderBy($item, $order, $column)
    {
        $columnValues = $this->getActualValuesInTableByColumnName($item, $column);

        $sortedColumnValues = $columnValues;
        'ascending' === $order ? sort($sortedColumnValues) : rsort($sortedColumnValues);

        assertSame($sortedColumnValues, $columnValues, sprintf('Failed asserting there are sorted "%s" "%s" in the list, in reality they are "%s"', print_r($sortedColumnValues, 1), $entity, print_r($columnValues, 1)));
    }

    /**
     * @Given /^there are following tax categories:$/
     */
    public function thereAreFollowingTaxCategories(TableNode $table)
    {
        $repository = $this->getService('sylius_taxation.repository.category');
        $manager = $this->getService('sylius_taxation.manager.category');

        foreach ($table->getHash() as $data) {
            $category = $repository->createNew();
            $category->setName($data['name']);

            $manager->persist($category);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are no tax categories$/
     */
    public function thereAreNoTaxCategories()
    {
        $repository = $this->getService('sylius_taxation.repository.category');
        $manager = $this->getService('sylius_taxation.manager.category');

        foreach ($repository->findAll() as $category) {
            $manager->remove($category);
        }

        $manager->flush();
    }

    /**
     * @Given /^I created tax category "([^""]*)"$/
     */
    public function iCreatedTaxCategory($name)
    {
        $repository = $this->getService('sylius_taxation.repository.category');
        $manager = $this->getService('sylius_taxation.manager.category');

        $category = $repository->createNew();
        $category->setName($name);

        $manager->persist($category);
        $manager->flush();
    }

    /**
     * @Given /^there are following tax rates:$/
     * @Given /^the following tax rates exist:$/
     */
    public function thereAreFollowingTaxRates(TableNode $table)
    {
        $repository = $this->getService('sylius_taxation.repository.rate');
        $manager = $this->getService('sylius_taxation.manager.category');

        foreach ($table->getHash() as $data) {
            $rate = $repository->createNew();

            $rate->setName($data['name']);
            $rate->setAmount($data['amount'] / 100);
            $rate->setCategory($this->findOneByName('tax_category', $data['category']));
            $rate->setCalculator('default');

            $manager->persist($rate);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are no tax rates$/
     */
    public function thereAreNoTaxRates()
    {
        $repository = $this->getService('sylius_taxation.repository.rate');
        $manager = $this->getService('sylius_taxation.manager.rate');

        foreach ($repository->findAll() as $rate) {
            $manager->remove($rate);
        }

        $manager->flush();
    }

    /**
     * @Given /^I created tax rate "([^""]*)" for category "([^""]*)" with amount (\d+)%$/
     */
    public function iCreatedTaxRateWithAmount($name, $category, $amount)
    {
        $repository = $this->getService('sylius_taxation.repository.rate');
        $manager = $this->getService('sylius_taxation.manager.rate');

        $rate = $repository->createNew();
        $rate->setName($name);
        $rate->setAmount($amount / 100);
        $rate->setCategory($this->findOneByName('tax_category', $category));
        $rate->setCalculator('default');

        $manager->persist($rate);
        $manager->flush();
    }

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    private function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Get language locale by canonical name.
     *
     * @param string $language
     *
     * @return string
     */
    private function getLanguageLocale($language)
    {
        $locales  = array('english' => 'en');
        $language = strtolower($language);

        if (!isset($locales[$language])) {
            throw new \Exception(sprintf('Unknown language "%s"', $language));
        }

        return $locales[$language];
    }

    /**
     * Generate page url.
     * This method uses simple convention where page argument is prefixed
     * with "sylius_" and used as route name passed to router generate method.
     *
     * @param string $page
     * @param array  $parameters
     *
     * @return string
     */
    private function generatePageUrl($page, array $parameters = array())
    {
        $parts = explode(' ', trim($page), 2);

        $route  = implode('_', $parts);
        $routes = $this->getContainer()->get('router')->getRouteCollection();

        if (null === $routes->get($route)) {
            $route = 'sylius_'.$route;
        }

        if (null === $routes->get($route)) {
            $route = str_replace('sylius_', 'sylius_backend_', $route);
        }

        $route = str_replace(array_keys($this->actions), array_values($this->actions), $route);
        $route = str_replace(' ', '_', $route);

        $path = $this->generateUrl($route, $parameters);

        if ('Selenium2Driver' === strstr(get_class($this->getSession()->getDriver()), 'Selenium2Driver')) {
            return sprintf('%s%s', $this->getMinkParameter('base_url'), $path);
        }

        return $path;
    }

    /**
     * Fetch all the values of a column inside a table
     *
     * @param $items      string The items name
     * @param $columnName string The name of the column
     *                           from where we're getting the values
     *
     * @throws ElementNotFoundException
     *
     * @return array The found values
     */
    protected function getActualValuesInTableByColumnName($items, $columnName)
    {
        $id = str_replace(' ', '-', Pluralization::pluralize($items));
        $rows = $this->getSession()->getPage()->findAll('css', sprintf('table#%s thead tr th', $id));

        foreach ($rows as $key => $row) {
            if ($row->getText() === $columnName) {
                return $this->getActualValuesInTable($items, $key);
            }
        }

        throw new ElementNotFoundException(
            $this->getSession(), 'table element', 'th', $columnName
        );
    }

    /**
     * Get total number of rows by item name.
     *
     * @param string $items
     *
     * @return integer
     */
    protected function getCountOfItemsByName($items)
    {
        $items = str_replace(' ', '-', Pluralization::pluralize($items));
        $nodes = $this->getSession()->getPage()->findAll('css', sprintf('tr.%s-row', $items));

        return count($nodes);
    }

    /**
     * Fetch all the values of a column inside a table
     *
     * @param $items  The name of the items to fetch
     * @param $column The index of the column
     *                from where we're getting the values
     *
     * @return array The found values
     */
    protected function getActualValuesInTable($items, $column)
    {
        $items = str_replace(' ', '-', Pluralization::pluralize($items));
        $rows = $this->getSession()->getPage()->findAll('css', sprintf('table#%s tbody tr', $items));

        $values = array();
        foreach ($rows as $row) {
            $cols = $row->findAll('css', 'td');
            $values[] = $cols[$column]->getText();
        }

        return $values;
    }

    /**
     * Generate url.
     *
     * @param string  $route
     * @param array   $parameters
     * @param Boolean $absolute
     *
     * @return string
     */
    protected function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->getContainer()->get('router')->generate($route, $parameters, $absolute);
    }

    /**
     * Get security context.
     *
     * @return SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

    /**
     * Get current user instance.
     *
     * @return null|UserInterface
     */
    protected function getUser()
    {
        $token = $this->getSecurityContext()->getToken();

        if (null === $token) {
            throw new \Exception('No token found in security context.');
        }

        return $token->getUser();
    }

    /**
     * Find one entity by type and name.
     *
     * @param string $type
     * @param string $name
     */
    protected function findOneByName($type, $name)
    {
        return $this
            ->getRepositoryByType($type)
            ->findOneBy(array('name' => $name))
        ;
    }

    /**
     * Get repository by type.
     *
     * @param string $type
     *
     * @return ObjectRepository
     */
    protected function getRepositoryByType($type)
    {
        return $this->getService($this->repositories[$type]);
    }

    /**
     * Get entity manager.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Get entity repository.
     *
     * @return EntityRepository
     */
    protected function getRepository($entity)
    {
        return $this->getEntityManager()->getRepository($entity);
    }

    /**
     * Get Symfony profiler.
     *
     * @return Profiler
     */
    protected function getProfiler()
    {
        return $this->getContainer()->get('profiler');
    }
}
