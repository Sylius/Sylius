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

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Doctrine\Common\Util\Inflector;
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
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
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
     * @Given /^I am on the ([\w\s]+)$/
     * @Given /^I am on the ([\w\s]+) page?$/
     * @Given /^I am on the ([\w\s]+) page in (\w+)$/
     * @When /^I go to the ([\w\s]+) page?$/
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
     * @Then /^I should be on the (.+)$/
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
     * @Given /^I am not authenticated$/
     * @Given /^I am not logged in anymore$/
     */
    public function iAmNotAuthenticated()
    {
        $this->getSecurityContext()->setToken(null);
        $this->getContainer()->get('session')->invalidate();
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
     * @Given /^I press "([^"]*)"$/
     */
    public function iPress($button)
    {
        $this->getSession()->getPage()->pressButton($button);
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
     * @Then /^(?:.* )?"([^"]*)" should appear on the page$/
     */
    public function shouldAppearOnThePage($text)
    {
        $this->assertSession()->pageTextContains($text);
    }

    /**
     * @Given /^I leave "([^"]*)" empty$/
     */
    public function iLeaveFieldEmpty($field)
    {
        $this->assertSession()->fieldValueEquals($field, '');
    }

    /**
     * @When /^I go to the website root$/
     */
    public function iGoToTheWebsiteRoot()
    {
        $this->getSession()->visit('/');
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

        if (2 === count($parts)) {
            $parts[1] = Inflector::camelize($parts[1]);
        }

        $route  = implode('_', $parts);
        $routes = $this->getContainer()->get('router')->getRouteCollection();

        if (null === $routes->get($route)) {
            $route = 'sylius_'.$route;
        }

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
