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
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Locale\Locale;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Web user context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class WebUser extends RawMinkContext implements KernelAwareInterface
{
    /**
     * Actions to route parts map.
     *
     * @var array
     */
    protected $actions = array(
        'viewing'  => 'show',
        'creation' => 'create',
        'editing'  => 'update',
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Sylius data creation context.
        $this->useContext('data', new DataContext());
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


    /**
     * @Given /^go to "([^""]*)" tab$/
     */
    public function goToTab($tabLabel)
    {
        $this->getSession()->getPage()->find('css', sprintf('.nav-tabs a:contains("%s")', $tabLabel))->click();
    }


    /**
     * @Given /^I add following option values:$/
     */
    public function iAddFollowingOptionValues(TableNode $table)
    {
        $count = count($this->getSession()->getPage()->findAll('css', 'div.collection-container div.control-group'));

        foreach ($table->getRows() as $i => $value) {
            $this->getSession()->getPage()->find('css', 'a:contains("Add value")')->click();
            $this->iFillInFieldWith(sprintf('sylius_option[values][%d][value]', $i+$count), $value[0]);
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
     * @Given /^I am on the (homepage)?$/
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
     * @Then /^I should be on the (homepage)?$/
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
     * @Given /^I go to the page of ([^""]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePage($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->getDataContext()->findOneByName($type, $name);

        $this->getSession()->visit($this->generatePageUrl(sprintf('sylius_backend_%s_show', $type), array('id' => $resource->getId())));
    }

    /**
     * @Then /^I should be on the page of ([^""]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePage($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->getDataContext()->findOneByName($type, $name);

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_show', $type), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I am editing ([^""]*) "([^""]*)"$/
     */
    public function iAmEditingResource($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->getDataContext()->findOneByName($type, $name);

        $this->getSession()->visit($this->generatePageUrl(sprintf('sylius_backend_%s_update', $type), array('id' => $resource->getId())));
    }

    /**
     * @Then /^I should be editing ([^""]*) "([^""]*)"$/
     */
    public function iShouldEditingResource($type, $name)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->getDataContext()->findOneByName($type, $name);

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_update', $type), array('id' => $resource->getId())));
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
     * Fills in form fields with provided table.
     *
     * @When /^(?:|I )fill in the following:$/
     */
    public function iFillInFieldsWith(TableNode $fields)
    {
        foreach ($fields->getRowsHash() as $field => $value) {
            $this->iFillInFieldWith($field, $value);
        }
    }

    /**
     * @Given /^I fill in province name with "([^"]*)"$/
     */
    public function iFillInProvinceNameWith($value)
    {
        $this->iFillInFieldWith('sylius_addressing_country[provinces][0][name]', $value);
    }

    /**
     * @When /^I click "([^"]*)" near "([^"]*)"$/
     */
    public function iClickNear($button, $value)
    {
        $tr = $this->getSession()->getPage()->find('css',
            sprintf('table tbody tr:contains("%s")', $value)
        );

        if (null === $tr) {
            throw new NotFoundHttpException(sprintf('Table row with value "%s" does not exist', $value));
        }

        if ($tr->findButton($button)) {
            $tr->pressButton($button);
        } else {
            $tr->clickLink($button);
        }
    }

    /**
     * @When /^I select "([^"]*)" from "([^"]*)"$/
     */
    public function iSelectOptionFrom($option, $field)
    {
        $this->getSession()->getPage()->selectFieldOption($field, $option);
    }

    /**
     * @When /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function additionallySelectOption($select, $option)
    {
        $this->getSession()->getPage()->selectFieldOption($select, $option, true);
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
        $this->getSession()->getPage()->fillField($field, '');
    }

    /**
     * @Given /^I press "([^"]*)"$/
     */
    public function iPress($button)
    {
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * For example: I should see product with name "Wine X" in that list.
     *
     * @Then /^I should see [\w\s]+ with [\w\s]+ "([^""]*)" in (that|the) list$/
     */
    public function iShouldSeeResourceWithValueInThatList($value)
    {
        $this->assertSession()->elementTextContains('css', 'table', $value);
    }

    /**
     * For example: I should not see product with name "Wine X" in that list.
     *
     * @Then /^I should not see [\w\s]+ with [\w\s]+ "([^""]*)" in (that|the) list$/
     */
    public function iShouldNotSeeResourceWithValueInThatList($value)
    {
        $this->assertSession()->elementTextNotContains('css', 'table', $value);
    }

    /**
     * For example: I should see 10 products in that list.
     *
     * @Then /^I should see (\d+) [^""]* in (that|the) list$/
     */
    public function iShouldSeeThatMuchResourcesInTheList($amount)
    {
        $this->assertSession()->elementsCount('css', 'table tbody tr', $amount);
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
     * Assert that given code equals the current one.
     *
     * @param integer $code
     */
    protected function assertStatusCodeEquals($code)
    {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            $this->assertSession()->statusCodeEquals($code);
        }
    }

    /**
     * Get data context.
     *
     * @return DataContext
     */
    protected function getDataContext()
    {
        return $this->getSubcontext('data');
    }

    /**
     * Assert that there is given count of elements on page.
     *
     * @param string  $selector
     * @param integer $count
     */
    protected function assertElementsCount($selector, $count)
    {
        $this->assertSession()->elementsCount('css', $selector, $count);
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
     * Get security context.
     *
     * @return SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
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
     * Get Symfony profiler.
     *
     * @return Profiler
     */
    protected function getProfiler()
    {
        return $this->getContainer()->get('profiler');
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
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->kernel->getContainer();
    }
}
