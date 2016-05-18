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

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\NodeElement;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Metadata\Model\Custom\PageMetadata;
use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataContainerInterface;
use Sylius\Component\Metadata\Model\Twitter\AppCard;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;
use Sylius\Component\Metadata\Model\Twitter\PlayerCard;
use Sylius\Component\Metadata\Model\Twitter\SummaryCard;
use Sylius\Component\Metadata\Model\Twitter\SummaryLargeImageCard;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContext extends DefaultContext
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    public function __construct($applicationName = null)
    {
        parent::__construct($applicationName);

        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * @When I am customizing metadata
     * @When I am customizing metadata with identifier :identifier
     */
    public function iAmCustomizingMetadata($identifier = 'FooBar')
    {
        $this->getSession()->visit($this->generateUrl('sylius_backend_metadata_container_customize', ['id' => $identifier]));
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
     * @Given there is the following metadata :metadataName:
     */
    public function thereIsTheFollowingMetadata($metadataName, TableNode $table)
    {
        $pageMetadata = new PageMetadata();
        foreach ($table->getRowsHash() as $key => $value) {
            if ($this->createNewMetadataObjectIfNeeded($pageMetadata, $key, $value)) {
                continue;
            }

            if (false !== strpos($value, ',')) {
                $value = array_map('trim', explode(',', $value));
            }

            $this->propertyAccessor->setValue($pageMetadata, $key, $value);
        }

        /** @var MetadataContainerInterface $metadata */
        $metadata = $this->getFactory('metadata_container')->createNew();
        $metadata->setId($metadataName);
        $metadata->setMetadata($pageMetadata);

        $em = $this->getEntityManager();
        $em->persist($metadata);
        $em->flush();
    }

    /**
     * @Given product :productName has the following page metadata:
     */
    public function productHasTheFollowingPageMetadata($productName, TableNode $table)
    {
        /** @var ProductInterface $product */
        $product = $this->getRepository('product')->findOneByName($productName);

        $this->thereIsTheFollowingMetadata($product->getMetadataIdentifier(), $table);
    }

    /**
     * @Then I should see :title as page title
     */
    public function iShouldSeeAsPageTitle($title)
    {
        $this->assertSession()->elementTextContains('css', 'title', $title);
    }

    /**
     * @Then the page keywords should contain :keyword
     */
    public function thePageKeywordsShouldContain($keyword)
    {
        $this->assertSession()->elementExists('css', sprintf('meta[name="keywords"][content*="%s"]', $keyword));
    }

    /**
     * @Then there should be Twitter summary card metadata on this page
     */
    public function thereShouldBeTwitterSummaryCardMetadataOnThisPage()
    {
        $this->assertSession()->elementExists('css', 'meta[name="twitter:card"][content="summary"]');
    }

    /**
     * @Then Twitter site should be :site
     */
    public function twitterSiteShouldBe($site)
    {
        $this->assertSession()->elementExists('css', sprintf('meta[name="twitter:site"][content="%s"]', $site));
    }

    /**
     * @Then Twitter image should be :image
     */
    public function twitterImageShouldBe($image)
    {
        $this->assertSession()->elementExists('css', sprintf('meta[name="twitter:image"][content="%s"]', $image));
    }

    /**
     * @When /I deselect "([^"]+)"/
     */
    public function iDeselectSelectField($fieldName)
    {
        $this->selectOption($fieldName, '');
    }

    /**
     * @param ElementInterface $element
     * @param string $regexp
     *
     * @throws \Exception If assertion failed
     */
    private function assertItIsMetadataCustomizationPage(ElementInterface $element, $regexp)
    {
        if (!$this->isItMetadataCustomizationPage($element, $regexp)) {
            throw new \Exception(sprintf('It is not metadata customization page (regexp: %s)', $regexp));
        }
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

        if (null === $header) {
            return false;
        }

        if (false === strpos($header->getText(), 'Customizing metadata')) {
            return false;
        }

        if (false === (bool) preg_match($regexp, $header->getText())) {
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
        if (null === $this->getFormWithFields($element, $fields)) {
            throw new \Exception(sprintf('Could not found table with fields: %s', implode(', ', $fields)));
        }
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

    /**
     * @param string $value
     *
     * @return CardInterface
     */
    protected function createTwitterCardFromString($value)
    {
        switch (strtolower($value)) {
            case 'summary':
                return new SummaryCard();

            case 'summary with large image':
            case 'summary large image':
            case 'summarylargeimage':
                return new SummaryLargeImageCard();

            case 'player':
                return new PlayerCard();

            case 'app':
            case 'application':
                return new AppCard();

            default:
                throw new \InvalidArgumentException(sprintf('Unknown card type "%s"!', $value));
        }
    }

    /**
     * @param PageMetadataInterface $pageMetadata
     * @param string $key
     * @param string $value
     *
     * @return bool True if created new metadata object
     */
    protected function createNewMetadataObjectIfNeeded(PageMetadataInterface $pageMetadata, $key, $value)
    {
        if ('twitter.card' === strtolower($key)) {
            $pageMetadata->setTwitter($this->createTwitterCardFromString($value));

            return true;
        }

        return false;
    }
}
