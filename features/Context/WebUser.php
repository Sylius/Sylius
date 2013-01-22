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
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Driver\Selenium2Driver;
use FOS\RestBundle\Util\Pluralization;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Locale\Locale;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;

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
        'country'      => 'sylius_addressing.repository.country',
        'zone'         => 'sylius_addressing.repository.zone',
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
     * @Then /^I should be on the (homepage)$/
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
        $this->assertStatusCodeEquals(200);
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
        if (null === $resource) {
            throw new ExpectationException(
                sprintf('%s with name "%s" not found.', ucfirst($type), $name),
                $this->getSession()
            );
        }

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_show', $type), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
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
     * @Then /^I should not see "([^"]*)"$/
     * @Then /^(?:.* )?"([^"]*)" should not appear on the page$/
     */
    public function iShouldNotSeeText($text)
    {
        $this->assertSession()->pageTextNotContains($text);
    }

    /**
     * @When /^I follow "([^"]+)"$/
     * @When /^I click "([^"]+)"$/
     */
    public function iClick($link)
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
     * @Given /^I fill in province name with "([^"]*)"$/
     */
    public function iFillInProvinceNameWith($value)
    {
        $this->iFillInFieldWith('sylius_addressing_country[provinces][0][name]', $value);
    }

    /**
     * @Given /^I delete ([^""]*) "([^"]*)"$/
     */
    public function iDeleteRowInTable($items, $value)
    {
        $column = ucfirst(Pluralization::pluralize($items));

        foreach ($this->getActualValuesInTableByColumnName($items, $column) as $position => $actual) {
            if ($actual === $value) {
                $elements = $this->getActualElementsInTable($items, count($this->getActualHeadersInTable($items)) - 1);
                $elements[$position]->findLink('Delete')->click();
                return;
            }
        }

        throw new ExpectationException(sprintf('Delete button not found for given %s.', $items), $this->getSession());
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
        $items = str_replace(' ', '-', Pluralization::pluralize($item));
        $this->assertElementsCount(sprintf('table tbody tr.%s-row', $items), $amount);
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
        $this->thereAreNoItems('sylius_taxation', 'category');
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
     * @Given /^I created country "([^""]*)"$/
     */
    public function iCreatedCountry($name)
    {
        $this->thereIsCountry($name);

        $this->getService('sylius_addressing.manager.country')->flush();
    }

    /**
     * @Given /^I created zone "([^"]*)"$/
     */
    public function iCreatedZone($name)
    {
        $this->thereIsZone($name, ZoneInterface::TYPE_COUNTRY);

        $this->getService('sylius_addressing.manager.zone')->flush();
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
        $this->thereAreNoItems('sylius_taxation', 'rate');
    }

    /**
     * @Given /^there are following zones:$/
     */
    public function thereAreFollowingZones(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $zone = $this->thereIsZone($data['name'], $data['type'], explode(',', $data['members']));
        }

        $this->getService('sylius_addressing.manager.zone')->flush();
    }

    /**
     * @Given /^there are no zones$/
     */
    public function thereAreNoZones()
    {
        $this->thereAreNoItems('sylius_addressing', 'zone');
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
     * @Given /^there are following countries:$/
     */
    public function thereAreFollowingCountries(TableNode $table)
    {
        $repository = $this->getService('sylius_addressing.repository.country');
        $manager = $this->getService('sylius_addressing.manager.country');
        $provinceRepository = $this->getService('sylius_addressing.repository.province');

        foreach ($table->getHash() as $data) {
            $this->thereisCountry($data['name'], explode(',', $data['provinces']));
        }

        $manager->flush();
    }

    /**
     * @Given /^there are no countries$/
     */
    public function thereAreNoCountries()
    {
        $this->thereAreNoItems('sylius_addressing', 'country');
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

    private function thereisCountry($name, array $provinces = array())
    {
        $country = $this->getService('sylius_addressing.repository.country')->createNew();

        $country->setName($name);
        $country->setIsoName(array_search($name, Locale::getDisplayCountries(Locale::getDefault())));

        foreach ($provinces as $provinceName) {
            $country->addProvince($this->thereisProvince($provinceName));
        }

        $this->getService('sylius_addressing.manager.country')->persist($country);

        return $country;
    }

    private function thereisProvince($name)
    {
        $province = $this->getService('sylius_addressing.repository.province')->createNew();
        $province->setName($name);

        $this->getService('sylius_addressing.manager.province')->persist($province);

        return $province;
    }

    private function thereIsZone($name, $type = ZoneInterface::TYPE_COUNTRY, array $members = array())
    {
        $repository = $this->getService('sylius_addressing.repository.zone');

        $zone = $repository->createNew();
        $zone->setName($name);
        $zone->setType($type);

        foreach ($members as $memberName) {
            $member = $this->getService('sylius_addressing.repository.zone_member_'.$type)->createNew();
            if (ZoneInterface::TYPE_ZONE === $type) {
                $zoneable = $repository->findOneByName($memberName);
            } else {
                $zoneable = call_user_func(array($this, 'thereIs'.ucfirst($type)), $memberName);
            }

            call_user_func(array(
                $member, 'set'.ucfirst($type)),
                $zoneable
            );

            $zone->addMember($member);
        }

        $this->getService('sylius_addressing.manager.zone')->persist($zone);

        return $zone;
    }

    protected function thereAreNoItems($prefix, $item)
    {
        $repository = $this->getService($prefix.'.repository.'.$item);
        $manager = $this->getService($prefix.'.manager.'.$item);

        foreach ($repository->findAll() as $rate) {
            $manager->remove($rate);
        }

        $manager->flush();
    }

    protected function assertStatusCodeEquals($code)
    {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            $this->assertSession()->statusCodeEquals($code);
        }
    }

    protected function assertElementsCount($selector, $count)
    {
        $this->assertSession()->elementsCount('css', $selector, $count);
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
        foreach ($this->getActualHeadersInTable($items) as $key => $row) {
            if ($row->getText() === $columnName) {
                return $this->getActualValuesInTable($items, $key);
            }
        }

        throw new ElementNotFoundException(
            $this->getSession(), 'table element', 'th', $columnName
        );
    }

    /**
     * Fetch all headers from table.
     *
     * @param $items string the items name
     *
     * @return array
     */
    protected function getActualHeadersInTable($items)
    {
        $id = str_replace(' ', '-', Pluralization::pluralize($items));

        return $this->getSession()->getPage()->findAll('css', sprintf('table#%s thead tr th', $id));
    }

    /**
     * Fetch all the elements of a column inside a table
     *
     * @param $items  The name of the items to fetch
     * @param $column The index of the column
     *                from where we're getting the values
     *
     * @return Behat\Mink\Element\NodeElement[]
     */
    protected function getActualElementsInTable($items, $column)
    {
        $items = str_replace(' ', '-', Pluralization::pluralize($items));
        $rows = $this->getSession()->getPage()->findAll('css', sprintf('table#%s tbody tr', $items));

        $elements = array();
        foreach ($rows as $row) {
            $cols = $row->findAll('css', 'td');
            $elements[] = $cols[$column];
        }

        return $elements;
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
        $values = array();
        foreach ($this->getActualElementsInTable($items, $column) as $element) {
            $values[] = $element->getText();
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
