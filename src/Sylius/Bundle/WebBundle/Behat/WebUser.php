<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Behat;

use Behat\Behat\Context\Step;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ExpectationException;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Locale\Locale;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Web user context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class WebUser extends MinkContext implements KernelAwareInterface
{
    /**
     * Actions.
     *
     * @var array
     */
    private $actions = array(
        'viewing'  => 'show',
        'creation' => 'create',
        'editing'  => 'update',
        'building' => 'build',
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
     * @Then /^the page title should be "([^""]*)"$/
     */
    public function thePageTitleShouldBe($title)
    {
        $this->assertSession()->elementTextContains('css', 'title', $title);
    }

    /**
     * @When /^I go to the website root$/
     */
    public function iGoToTheWebsiteRoot()
    {
        $this->getSession()->visit('/');
    }

    /**
     * @Given /^I am on the (.+) (page|step)?$/
     * @When /^I go to the (.+) (page|step)?$/
     */
    public function iAmOnThePage($page)
    {
        $this->getSession()->visit($this->generatePageUrl($page));
    }

    /**
     * @Then /^I should be on the (.+) (page|step)$/
     * @Then /^I should be redirected to the (.+) (page|step)$/
     * @Then /^I should still be on the (.+) (page|step)$/
     */
    public function iShouldBeOnThePage($page)
    {
        $this->assertSession()->addressEquals($this->generatePageUrl($page));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I should be on the store homepage$/
     */
    public function iShouldBeOnTheStoreHomepage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('sylius_homepage'));
    }

    /**
     * @Given /^I am on the store homepage$/
     */
    public function iAmOnTheStoreHomepage()
    {
        $this->getSession()->visit($this->generateUrl('sylius_homepage'));
    }

    /**
     * @Given /^I am on my account homepage$/
     */
    public function iAmOnMyAccountHomepage()
    {
        $this->getSession()->visit($this->generateUrl('sylius_account_homepage'));
    }

    /**
     * @Given /^I should be on my account homepage$/
     */
    public function iShouldBeOnMyAccountHomepage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('sylius_account_homepage'));
    }

    /**
     * @Given /^I am on my account password page$/
     */
    public function iAmOnMyAccountPasswordPage()
    {
        $this->getSession()->visit($this->generateUrl('fos_user_change_password'));
    }

    /**
     * @Given /^I should be on my account password page$/
     */
    public function iShouldBeOnMyAccountPasswordPage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('fos_user_change_password'));
    }

    /**
     * @Then /^I should still be on my account password page$/
     */
    public function iShouldStillBeOnMyAccountPasswordPage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('fos_user_change_password'));
    }

    /**
     * @Given /^I am on my account profile edition page$/
     */
    public function iAmOnMyAccountProfileEditionPage()
    {
        $this->getSession()->visit($this->generateUrl('fos_user_profile_edit'));
    }

    /**
     * @Given /^I should be on my account profile edition page$/
     */
    public function iShouldBeOnMyProfileEditionPage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('fos_user_profile_edit'));
    }

    /**
     * @Given /^I should still be on my account profile edition page$/
     */
    public function iShouldStillBeOnMyProfileEditionPage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('fos_user_profile_edit'));
    }

    /**
     * @Given /^I should be on my account profile page$/
     */
    public function iShouldBeOnMyProfilePage()
    {
        $this->assertSession()->addressEquals($this->generateUrl('fos_user_profile_show'));
    }

    /**
     * @Then /^I should be on login page$/
     */
    public function iShouldBeOnLoginPage()
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('fos_user_security_login'));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on registration page$/
     */
    public function iShouldBeOnRegistrationPage()
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('fos_user_registration_register'));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I am on the shipment page with method "([^""]*)"$/
     */
    public function iAmOnTheShipmentPage($value)
    {
        $shippingMethod = $this->getDataContext()->findOneBy('shipping_method', array('name' => $value));
        $shipment = $this->getDataContext()->findOneBy('shipment', array('method' => $shippingMethod));

        $this->getSession()->visit($this->generatePageUrl('backend_shipment_show', array('id' => $shipment->getId())));
    }

    /**
     * @Given /^I am on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $resource = $this->getDataContext()->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generatePageUrl(sprintf('backend_%s_show', $type), array('id' => $resource->getId())));
    }

    /**
     * @Given /^I am on the page of ([^""(w)]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePageByName($type, $name)
    {
        $this->iAmOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Then /^I should be on the shipment page with method "([^"]*)"$/
     */
    public function iShouldBeOnTheShipmentPageWithMethod($value)
    {
        $shippingMethod = $this->getDataContext()->findOneBy('shipping_method', array('name' => $value));
        $shipment = $this->getDataContext()->findOneBy('shipment', array('method' => $shippingMethod));

        $this->assertSession()->addressEquals($this->generatePageUrl('backend_shipment_show', array('id' => $shipment->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->getDataContext()->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('backend_%s_show', $type), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on the page of ([^""(w)]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePageByName($type, $name)
    {
        $this->iShouldBeOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->getDataContext()->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generatePageUrl(sprintf('backend_%s_%s', $type, $action), array('id' => $resource->getId())));
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResourceByName($action, $type, $name)
    {
        $this->iAmDoingSomethingWithResource($action, $type, 'name', $name);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^"]*) with ([^"]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->getDataContext()->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_%s', $type, $action), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResourceByName($action, $type, $name)
    {
        $this->iShouldBeDoingSomethingWithResource($action, $type, 'name', $name);
    }

    /**
     * @Given /^I am creating variant of "([^""]*)"$/
     */
    public function iAmCreatingVariantOf($name)
    {
        $product = $this->getDataContext()->findOneByName('product', $name);

        $this->getSession()->visit($this->generatePageUrl('sylius_backend_variant_create', array('productId' => $product->getId())));
    }

    /**
     * @Given /^I should be creating variant of "([^""]*)"$/
     */
    public function iShouldBeCreatingVariantOf($name)
    {
        $product = $this->getDataContext()->findOneByName('product', $name);

        $this->assertSession()->addressEquals($this->generatePageUrl('sylius_backend_variant_create', array('productId' => $product->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I added product "([^""]*)" to cart$/
     */
    public function iAddedProductToCart($productName)
    {
        $this->iAmOnTheProductPage($productName);
        $this->pressButton('Add to cart');
    }

    /**
     * @Then /^(?:.* )?"([^"]*)" should appear on the page$/
     */
    public function textShouldAppearOnThePage($text)
    {
        $this->assertSession()->pageTextContains($text);
    }

    /**
     * @Then /^(?:.* )?"([^"]*)" should not appear on the page$/
     */
    public function textShouldNotAppearOnThePage($text)
    {
        $this->assertSession()->pageTextNotContains($text);
    }

    /**
     * @When /^I click "([^"]+)"$/
     */
    public function iClick($link)
    {
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * @Given /^I fill in province name with "([^"]*)"$/
     */
    public function iFillInProvinceNameWith($value)
    {
        $this->fillField('sylius_country[provinces][0][name]', $value);
    }

    /**
     * @Given /^I fill in the (billing|shipping) address to (.+)$/
     */
    public function iFillInCheckoutAddress($type, $country)
    {
        $base = sprintf('sylius_checkout_addressing[%sAddress]', $type);

        $this->fillField($base.'[firstName]', 'John');
        $this->fillField($base.'[lastName]', 'Doe');
        $this->fillField($base.'[street]', 'Pvt. Street 15');
        $this->fillField($base.'[city]', 'Lodz');
        $this->fillField($base.'[postcode]', '95-253');
        $this->selectOption($base.'[country]', $country);
    }

    /**
     * @Given /^I select the "(?P<field>([^""]|\\")*)" radio button$/
     */
    public function iSelectTheRadioButton($field)
    {
        $field = str_replace('\\"', '"', $field);
        $radio = $this->getSession()->getPage()->findField($field);

        if (null === $radio) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        $this->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    /**
     * @When /^I click "([^"]*)" near "([^"]*)"$/
     * @When /^I press "([^"]*)" near "([^"]*)"$/
     */
    public function iClickNear($button, $value)
    {
        $tr = $this->getSession()->getPage()->find('css',
            sprintf('table tbody tr:contains("%s")', $value)
        );

        if (null === $tr) {
            throw new ExpectationException(sprintf('Table row with value "%s" does not exist', $value), $this->getSession());
        }

        $locator = sprintf('button:contains("%s")', $button);

        if ($tr->has('css', $locator)) {
            $tr->find('css', $locator)->press();
        } else {
            $tr->clickLink($button);
        }
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
     * @Given /^I leave "([^"]*)" field blank/
     */
    public function iLeaveFieldEmpty($field)
    {
        $this->getSession()->getPage()->fillField($field, '');
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
     * @Then /^I should see (\d+) ([^""]*) in (that|the) list$/
     */
    public function iShouldSeeThatMuchResourcesInTheList($amount, $type)
    {
        if (1 === count($this->getSession()->getPage()->findAll('css', 'table'))) {
            $this->assertSession()->elementsCount('css', 'table tbody tr', $amount);
        } else {
            $this->assertSession()->elementsCount('css', sprintf('table#%s tbody tr', str_replace(' ', '-', $type)), $amount);
        }
    }

    /**
     * For example: I should see 10 products.
     *
     * @Then /^I should see there (\d+) products/
     */
    public function iShouldSeeThatMuchProducts($amount)
    {
        $this->assertSession()->elementsCount('css', '.product', $amount);
    }

    /**
     * @Given /^I am on the product page for "([^"]*)"$/
     * @Given /^I go to the product page for "([^"]*)"$/
     */
    public function iAmOnTheProductPage($name)
    {
        $product = $this->getDataContext()->findOneBy('product', array('name' => $name));

        $this->getSession()->visit($this->generatePageUrl('sylius_product_show', array('slug' => $product->getSlug())));
    }

    /**
     * @Then /^I should be on the product page for "([^"]*)"$/
     * @Then /^I should still be on the product page for "([^"]*)"$/
     */
    public function iShouldBeOnTheProductPage($name)
    {
        $product = $this->getDataContext()->findOneBy('product', array('name' => $name));

        $this->assertSession()->addressEquals($this->generatePageUrl('sylius_product_show', array('slug' => $product->getSlug())));
        $this->assertStatusCodeEquals(200);
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
        $this->iAmLoggedInAsRole('ROLE_SYLIUS_ADMIN');
    }

    /**
     * @Given /^I am logged in user$/
     */
    public function iAmLoggedInUser()
    {
        $this->iAmLoggedInAsRole('ROLE_USER');
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
     * @Given /^I add following option values:$/
     */
    public function iAddFollowingOptionValues(TableNode $table)
    {
        $count = count($this->getSession()->getPage()->findAll('css', 'div.collection-container div.control-group'));

        foreach ($table->getRows() as $i => $value) {
            $this->getSession()->getPage()->find('css', 'a:contains("Add value")')->click();
            $this->fillField(sprintf('sylius_option[values][%d][value]', $i+$count), $value[0]);
        }
    }

    /**
     * Assert that given code equals the current one.
     *
     * @param integer $code
     */
    private function assertStatusCodeEquals($code)
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
    private function getDataContext()
    {
        return $this->getSubContext('data');
    }

    /**
     * Get current user instance.
     *
     * @return null|UserInterface
     */
    private function getUser()
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
    private function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

    /**
     * Create user and login with given role.
     *
     * @param string $role
     */
    private function iAmLoggedInAsRole($role)
    {
        $this->getSubContext('data')->thereIsUser('username', 'password', $role);
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_login'));

        $this->fillField('Login', 'username');
        $this->fillField('Password', 'password');
        $this->pressButton('login');
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
    private function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->getService('router')->generate($route, $parameters, $absolute);
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
