<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Behat;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\NodeElement;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContext extends DefaultContext
{
    /**
     * @When I am customizing metadata
     * @When I am customizing metadata with identifier "([^"]+)"
     */
    public function iAmCustomizingMetadata($identifier = 'FooBar')
    {
        $this->getSession()->visit($this->generateUrl('sylius_backend_metadata_customize', ['id' => $identifier]));
    }

    /**
     * @Then I should see metadata customization form
     */
    public function iShouldSeeMetadataCustomizationForm()
    {
        $this->assertThereIsFormWithFields(
            $this->getSession()->getPage(),
            ['Title', 'Description', 'Keywords', 'Twitter Card']
        );
    }

    /**
     * @Then I should see the following metadata:
     */
    public function iShouldSeeTheFollowingMetadata(TableNode $metadata)
    {
        /** @var NodeElement $table */
        $table = $this->getSession()->getPage()->find('css', '#content > table');

        /** @var NodeElement $row */
        $row = $table->findAll('css', 'tr')[1];

        /** @var NodeElement[] $columns */
        $columns = $row->findAll('css', 'td');

        $contentIndex = $this->getColumnIndex($table, 'Content');

        /** @var NodeElement $list */
        $list = $columns[$contentIndex];
        foreach ($metadata->getRowsHash() as $key => $value) {
            $currentElement = $list;
            $parts = explode('.', $key);

            foreach ($parts as $part) {
                $currentElement = $currentElement->find('xpath', sprintf('/ul/li[starts-with(normalize-space(.), "%s:")]', $part));
            }

            $exploded = explode(':', $currentElement->getText());
            $text = trim(end($exploded));

            $expectedValue = $value;
            if ('<empty>' === $expectedValue) {
                $expectedValue = 'empty';
            } elseif (false !== strpos($expectedValue, ',')) {
                $expectedValue = str_replace([', ', ','], [' ', ' '], $expectedValue);
            }

            if ($text !== $expectedValue) {
                throw new \Exception(sprintf(
                    'Expected "%s", got "%s" (item: "%s", original value: "%s")',
                    $expectedValue,
                    $text,
                    $key,
                    $value
                ));
            }
        }
    }

    /**
     * @Then I should be customizing default metadata
     */
    public function iShouldBeCustomizingDefaultMetadata()
    {
        $this->assertItIsMetadataCustomizationPage(
            $this->getSession()->getPage(),
            '/DefaultPage/'
        );
    }

    /**
     * @Then I should be customizing products metadata
     */
    public function iShouldBeCustomizingProductsMetadata()
    {
        $this->assertItIsMetadataCustomizationPage(
            $this->getSession()->getPage(),
            '/Product(?!\-)/'
        );
    }

    /**
     * @Then I should be customizing specific product metadata
     */
    public function iShouldBeCustomizingSpecificProductMetadata()
    {
        $this->assertItIsMetadataCustomizationPage(
            $this->getSession()->getPage(),
            '/Product-\d+/'
        );
    }

    /**
     * @Then I should be customizing specific product variant metadata
     */
    public function iShouldBeCustomizingSpecificProductVariantMetadata()
    {
        $this->assertItIsMetadataCustomizationPage(
            $this->getSession()->getPage(),
            '/ProductVariant-\d+/'
        );
    }

    /**
     * @Then I should see Twitter's application card form
     */
    public function iShouldSeeTwitterApplicationCardForm()
    {
        $this->assertSession()->fieldExists('Iphone application name');
        $this->assertSession()->fieldExists('Ipad application url');
    }

    /**
     * @Then I should not see Twitter's application card form
     */
    public function iShouldNotSeeTwitterApplicationCardForm()
    {
        $this->assertSession()->fieldNotExists('Iphone application name');
        $this->assertSession()->fieldNotExists('Ipad application url');
    }

    /**
     * @When /I deselect "([^"]+)"/
     */
    public function iDeselectSelectField($fieldName)
    {
        $this->getSession()->getPage()->selectFieldOption($fieldName, "");
    }

    /**
     * @param ElementInterface $element
     * @param string $regexp
     *
     * @throws \Exception If assertion failed
     */
    private function assertItIsMetadataCustomizationPage(ElementInterface $element, $regexp)
    {
        if ($this->isItMetadataCustomizationPage($element, $regexp)) {
            return;
        }

        throw new \Exception(sprintf("It is not metadata customziation page (regexp: %s)", $regexp));
    }

    /**
     * @param ElementInterface $element
     * @param string $regexp
     *
     * @return bool
     */
    private function isItMetadataCustomizationPage(ElementInterface $element, $regexp)
    {
        $header = $element->find('css', '.page-header h1');

        if (false === strpos($header->getText(), "Customizing metadata")) {
            return false;
        }

        if (!preg_match($regexp, $header->getText())) {
            return false;
        }

        return true;
    }

    /**
     * @param ElementInterface $element
     * @param array $fields
     *
     * @throws \Exception If assertion failed
     */
    private function assertThereIsFormWithFields(ElementInterface $element, array $fields)
    {
        if (null !== $this->getFormWithFields($element, $fields)) {
            return;
        }

        throw new \Exception(sprintf("Could not found table with fields: %s", join(', ', $fields)));
    }

    /**
     * @param ElementInterface $element
     * @param string[] $fields
     *
     * @return ElementInterface|null
     */
    private function getFormWithFields(ElementInterface $element, array $fields)
    {
        /** @var NodeElement[] $forms */
        $forms = $element->findAll('css', 'form');

        foreach ($forms as $form) {
            $found = true;
            foreach ($fields as $field) {
                if (null === $form->findField($field)) {
                    $found = false;
                }
            }

            if ($found) {
                return $form;
            }
        }

        return null;
    }
}
