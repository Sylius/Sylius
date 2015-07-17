<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class WebContext extends DefaultContext
{
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

        try {
            $this->assertStatusCodeEquals(200);
        } catch (UnsupportedDriverActionException $e) {
        }
    }

    /**
     * @Given /^I am on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $entityManager = $this->getEntityManager();
        $entityManager->getFilters()->disable('softdeleteable');
        $resource = $this->findOneBy($type, array($property => $value));
        $entityManager->getFilters()->enable('softdeleteable');

        $this->getSession()->visit($this->generatePageUrl(
            sprintf('%s_show', $type), array('id' => $resource->getId())
        ));
    }

    /**
     * @Given /^I am on the page of ([^""(w)]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePageByName($type, $name)
    {
        if ('country' === $type) {
            $this->iAmOnTheCountryPageByName($name);

            return;
        }

        $this->iAmOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Then /^I should be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $entityManager = $this->getEntityManager();
        $entityManager->getFilters()->disable('softdeleteable');
        $resource = $this->findOneBy($type, array($property => $value));
        $entityManager->getFilters()->enable('softdeleteable');

        $this->assertSession()->addressEquals($this->generatePageUrl(
            sprintf('%s_show', $type), array('id' => $resource->getId())
        ));

        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on the page of ([^""(w)]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePageByName($type, $name)
    {
        if ('country' === $type) {
            $this->iShouldBeOnTheCountryPageByName($name);

            return;
        }

        $this->iShouldBeOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""]*) with ([^""]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generatePageUrl(
            sprintf('%s_%s', $type, $action), array('id' => $resource->getId())
        ));
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResourceByName($action, $type, $name)
    {
        if ('country' === $type) {
            $this->iAmDoingSomethingWithCountryByName($action, $name);

            return;
        }

        $this->iAmDoingSomethingWithResource($action, $type, 'name', $name);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^"]*) with ([^"]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generatePageUrl(
            sprintf('%s_%s', $type, $action), array('id' => $resource->getId())
        ));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResourceByName($action, $type, $name)
    {
        if ('country' === $type) {
            $this->iShouldBeDoingSomethingWithCountryByName($action, $name);

            return;
        }

        $this->iShouldBeDoingSomethingWithResource($action, $type, 'name', $name);
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
        $this->clickLink($link);
    }

    /**
     * @Given /^I should see an? "(?P<element>[^"]*)" element near "([^"]*)"$/
     */
    public function iShouldSeeAElementNear($element, $value)
    {
        $tr = $this->assertSession()->elementExists('css', sprintf('table tbody tr:contains("%s")', $value));
        $this->assertSession()->elementExists('css', $element, $tr);
    }

    /**
     * @When /^I click "([^"]*)" near "([^"]*)"$/
     * @When /^I press "([^"]*)" near "([^"]*)"$/
     */
    public function iClickNear($button, $value)
    {
        $tr = $this->assertSession()->elementExists('css', sprintf('table tbody tr:contains("%s")', $value));

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
     * @Given /^I should see (\d+) validation errors$/
     */
    public function iShouldSeeFieldsOnError($amount)
    {
        $this->assertSession()->elementsCount('css', '.form-error', $amount);
    }

    /**
     * @Given /^I leave "([^"]*)" empty$/
     * @Given /^I leave "([^"]*)" field blank/
     */
    public function iLeaveFieldEmpty($field)
    {
        $this->fillField($field, '');
    }

    /**
     * For example: I should see product with name "Wine X" in that list.
     *
     * @Then /^I should see (?:(?!enabled|disabled)[\w\s]+) with ((?:(?![\w\s]+ containing))[\w\s]+) "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldSeeResourceWithValueInThatList($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName)),
            array(trim($value)),
        ));

        $this->iShouldSeeTheFollowingRow($tableNode);
    }

    /**
     * For example: I should not see product with name "Wine X" in that list.
     *
     * @Then /^I should not see [\w\s]+ with ((?:(?![\w\s]+ containing))[\w\s]+) "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldNotSeeResourceWithValueInThatList($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName)),
            array(trim($value)),
        ));

        $this->iShouldNotSeeTheFollowingRow($tableNode);
    }

    /**
     * For example: I should see product with name containing "Wine X" in that list.
     *
     * @Then /^I should see (?:(?!enabled|disabled)[\w\s]+) with ([\w\s]+) containing "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldSeeResourceWithValueContainingInThatList($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName)),
            array(trim('%' . $value . '%')),
        ));

        $this->iShouldSeeTheFollowingRow($tableNode);
    }

    /**
     * For example: I should not see product with name containing "Wine X" in that list.
     *
     * @Then /^I should not see [\w\s]+ with ([\w\s]+) containing "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldNotSeeResourceWithValueContainingInThatList($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName)),
            array(trim('%' . $value . '%')),
        ));

        $this->iShouldNotSeeTheFollowingRow($tableNode);
    }

    /**
     * For example: I should see 10 products in that list.
     *
     * @Then /^I should see (\d+) ([^""]*) in (?:that|the) list$/
     */
    public function iShouldSeeThatMuchResourcesInTheList($amount, $type)
    {
        if (1 === count($this->getSession()->getPage()->findAll('css', 'table'))) {
            $this->assertSession()->elementsCount('css', 'table tbody > tr', $amount);
        } else {
            $this->assertSession()->elementsCount(
                'css',
                sprintf('table#%s tbody > tr', str_replace(' ', '-', $type)),
                $amount
            );
        }
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
     * @Given /^I click "([^"]*)" from the confirmation modal$/
     */
    public function iClickOnConfirmationModal($button)
    {
        $this->assertSession()->elementExists('css', '#confirmation-modal');

        $modalContainer = $this->getSession()->getPage()->find('css', '#confirmation-modal');
        $primaryButton = $modalContainer->find('css', sprintf('a:contains("%s")' ,$button));

        $this->getSession()->wait(100);

        if (!preg_match('/in/', $modalContainer->getAttribute('class'))) {
            throw new \Exception('The confirmation modal was not opened...');
        }

        $this->getSession()->wait(100);

        $primaryButton->press();
    }

    /**
     * @Given /^I wait (\d+) (seconds|second)$/
     */
    public function iWait($time)
    {
        $this->getSession()->wait($time*1000);
    }

    /**
     * @Then I should have my access denied
     */
    public function iShouldHaveMyAccessDenied()
    {
        $this->assertStatusCodeEquals(403);
    }

    /**
     * @Then /^I should see enabled [\w\s]+ with ([\w\s]+) "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldSeeResourceInTheListAsEnabled($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName), 'Enabled'),
            array(trim($value), 'YES')
        ));

        $this->iShouldSeeTheFollowingRow($tableNode);
    }

    /**
     * @Then /^I should see disabled [\w\s]+ with ([\w\s]+) "([^""]*)" in (?:that|the) list$/
     */
    public function iShouldSeeResourceInTheListAsDisabled($columnName, $value)
    {
        $tableNode = new TableNode(array(
            array(trim($columnName), 'Enabled'),
            array(trim($value), 'NO')
        ));

        $this->iShouldSeeTheFollowingRow($tableNode);
    }

    /**
     * @Then /^I should see the following (?:row|rows):$/
     */
    public function iShouldSeeTheFollowingRow(TableNode $tableNode)
    {
        $table = $this->assertSession()->elementExists('css', 'table');

        foreach ($tableNode->getHash() as $fields) {
            if (null === $this->getRowWithFields($table, $fields)) {
                throw new \Exception('Table with given fields was not found!');
            }
        }
    }

    /**
     * @Then /^I should not see the following (?:row|rows):$/
     */
    public function iShouldNotSeeTheFollowingRow(TableNode $tableNode)
    {
        $table = $this->assertSession()->elementExists('css', 'table');

        foreach ($tableNode->getHash() as $fields) {
            if (null !== $this->getRowWithFields($table, $fields)) {
                throw new \Exception('Table with given fields was found!');
            }
        }
    }

    /**
     * @Then /^I should see ([\w\s]+) "([^""]*)" as available choice$/
     */
    public function iShouldSeeSelectWithOption($fieldName, $fieldOption)
    {
        /** @var NodeElement $select */
        $select = $this->assertSession()->fieldExists($fieldName);

        $selector = sprintf('option:contains("%s")', $fieldOption);
        $option = $select->find('css', $selector);

        if (null === $option) {
            throw new \Exception(sprintf('Option "%s" was not found!', $fieldOption));
        }
    }

    /**
     * @Then /^I should not see ([\w\s]+) "([^""]*)" as available choice$/
     */
    public function iShouldNotSeeSelectWithOption($fieldName, $fieldOption)
    {
        /** @var NodeElement $select */
        $select = $this->assertSession()->fieldExists(ucfirst($fieldName));

        $selector = sprintf('option:contains("%s")', $fieldOption);
        $option = $select->find('css', $selector);

        if (null !== $option) {
            throw new \Exception(sprintf('Option "%s" was found!', $fieldOption));
        }
    }

    /**
     * Assert that given code equals the current one.
     *
     * @param integer $code
     */
    protected function assertStatusCodeEquals($code)
    {
        $this->assertSession()->statusCodeEquals($code);
    }

    /**
     * @param string $name
     */
    private function iAmOnTheCountryPageByName($name)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        $this->iAmOnTheResourcePage('country', 'isoName', $isoName);
    }

    /**
     * @param string $action
     * @param string $name
     */
    private function iShouldBeDoingSomethingWithCountryByName($action, $name)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        $this->iShouldBeDoingSomethingWithResource($action, 'country', 'isoName', $isoName);
    }

    /**
     * @param string $action
     * @param string $name
     */
    private function iAmDoingSomethingWithCountryByName($action, $name)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        $this->iAmDoingSomethingWithResource($action, 'country', 'isoName', $isoName);
    }

    /**
     * @param string $name
     */
    private function iShouldBeOnTheCountryPageByName($name)
    {
        $isoName = $this->getCountryCodeByEnglishCountryName($name);

        $this->iShouldBeOnTheResourcePage('country', 'isoName', $isoName);
    }

    /**
     * @param NodeElement $modalContainer
     *
     * @throws \Exception
     */
    protected function waitForModalToAppear($modalContainer)
    {
        $i = 0;
        while (false === strpos($modalContainer->getAttribute('class'), 'in')) {
            if (10 === $i) {
                throw new \Exception('The confirmation modal was not opened...');
            }

            $this->getSession()->wait(100);
            ++$i;
        }
    }
}