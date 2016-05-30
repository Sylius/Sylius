<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Core\Model\ProductInterface;

class ProductContext extends DefaultContext
{
    /**
     * @Given /^there are products:$/
     * @Given /^there are following products:$/
     * @Given /^the following products exist:$/
     */
    public function thereAreProducts(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('product');

        foreach ($table->getHash() as $data) {
            $product = $factory->createWithVariant();

            $product->setCurrentLocale($this->getContainer()->getParameter('locale'));
            $product->setName(trim($data['name']));

            $code = isset($data['code']) ? $data['code'] : $this->generateCode($data['name']);
            $product->setCode($code);

            $product->getFirstVariant()->setCode($code.'-VARIANT');
            $product->getFirstVariant()->setPrice((int) round($data['price'] * 100));

            if (!empty($data['options'])) {
                foreach (explode(',', $data['options']) as $option) {
                    $option = $this->findOneBy('product_option', ['code' => trim($option)]);
                    $product->addOption($option);
                }
            }

            if (!empty($data['attributes'])) {
                $attribute = explode(':', $data['attributes']);

                $productAttribute = $this->findOneByName('product_attribute', trim($attribute[0]));
                $attributeValue = $this->getFactory('product_attribute_value')->createNew();

                $attributeValue->setAttribute($productAttribute);
                $attributeValue->setValue($attribute[1]);

                $product->addAttribute($attributeValue);
            }

            if (isset($data['description'])) {
                $product->setDescription($data['description']);
            }

            if (isset($data['quantity'])) {
                $product->getFirstVariant()->setOnHand($data['quantity']);
            }

            if (isset($data['variants selection']) && !empty($data['variants selection'])) {
                $product->setVariantSelectionMethod($data['variants selection']);
            }

            if (isset($data['tax category'])) {
                $product->getFirstVariant()->setTaxCategory($this->findOneByName('tax_category', trim($data['tax category'])));
            }

            if (isset($data['taxons'])) {
                $taxons = new ArrayCollection();

                foreach (explode(',', $data['taxons']) as $taxonName) {
                    $taxons->add($this->findOneByName('taxon', trim($taxonName)));
                }

                $product->setTaxons($taxons);
            }

            if (isset($data['pricing calculator']) && '' !== $data['pricing calculator']) {
                $this->configureProductPricingCalculator($product, $data);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @Given /^there is archetype "([^""]*)" with following configuration:$/
     */
    public function thereIsArchetypeWithFollowingConfiguration($name, TableNode $table)
    {
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('product_archetype');
        $data = $table->getRowsHash();

        $archetype = $factory->createNew();
        $archetype->setName($name);
        $archetype->setCode($data['code']);

        foreach (explode(',', $data['options']) as $optionName) {
            $option = $this->findOneBy('product_option', ['code' => trim($optionName)]);
            $archetype->addOption($option);
        }

        foreach (explode(',', $data['attributes']) as $attributeName) {
            $archetype->addAttribute($this->findOneByName('product_attribute', trim($attributeName)));
        }

        $manager->persist($archetype);
        $manager->flush();
    }

    /**
     * @Then :locale translation for product archetype :archetypeName should exist
     */
    public function translationForProductArchetypeShouldExist($locale, $archetypeName)
    {
        $archetype = $this->findOneByName('product_archetype_translation', $archetypeName);

        if (!$archetype->getLocale() === $locale) {
            throw new \Exception('There is no translation for product archetype'.$archetypeName.' in '.$locale.'locale');
        }
    }

    /**
     * @Given /^there are following options:$/
     * @Given /^the following options exist:$/
     */
    public function thereAreOptions(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsOption($data['name'], $data['values'], $data['code'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created option "([^""]*)" with values "([^""]*)" and option code "([^""]*)"$/
     */
    public function thereIsOption($name, $values, $optionCode, $flush = true)
    {
        $optionValueFactory = $this->getFactory('product_option_value');

        $option = $this->getFactory('product_option')->createNew();
        $option->setCode($optionCode);
        $option->setName($name ?: $optionCode );

        foreach (explode(',', $values) as $valueData) {
            $valueData = preg_split('[\\[|\\]]', $valueData, -1, PREG_SPLIT_NO_EMPTY);
            $optionValue = $optionValueFactory->createNew();
            $optionValue->setFallbackLocale($this->getContainer()->getParameter('locale'));
            $optionValue->setCurrentLocale($this->getContainer()->getParameter('locale'));
            $optionValue->setValue(trim($valueData[0]));
            $optionValue->setCode(trim($valueData[1]));

            $option->addValue($optionValue);
        }

        $manager = $this->getEntityManager();
        $manager->persist($option);

        if ($flush) {
            $manager->flush();
        }

        return $option;
    }

    /**
     * @Given /^there are following attributes:$/
     * @Given /^the following attributes exist:$/
     */
    public function thereAreAttributes(TableNode $table)
    {
        foreach ($table->getHash() as $attribute) {
            $this->thereIsAttribute(
                $attribute['name'],
                $attribute['type'],
                (isset($attribute['code'])) ? $attribute['code'] : null,
                (isset($attribute['configuration'])) ? $attribute['configuration'] : null
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^There is attribute "([^""]*)" with type "([^""]*)"$/
     * @Given /^I created attribute "([^""]*)" with type "([^""]*)"$/
     */
    public function thereIsAttribute($name, $type, $code = null, $configuration = null)
    {
        $code = (null === $code) ? strtolower(str_replace(' ', '_', $name)) : $code;
        $storageType = (CheckboxAttributeType::TYPE === $type) ? 'boolean' : $type;

        $attribute = $this->getFactory('product_attribute')->createNew();
        $attribute->setName($name);
        $attribute->setType($type);
        $attribute->setCode($code);
        $attribute->setStorageType($storageType);

        if (null !== $configuration && '' !== $configuration) {
            $attribute->setConfiguration($this->getConfiguration($configuration));
        }

        $manager = $this->getEntityManager();
        $manager->persist($attribute);

        return $attribute;
    }

    /**
     * @Given /^the following product translations exist:$/
     */
    public function theFollowingProductTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $productTranslation = $this->findOneByName('product_translation', $data['product']);

            $product = $productTranslation->getTranslatable();
            $product->setCurrentLocale($data['locale']);
            $product->setFallbackLocale($data['locale']);
            $product->setName($data['name']);
            $product->setDescription('...');
        }

        $manager->flush();
    }

    /**
     * @Then :locale translation for product :productName should exist
     */
    public function translationForProductShouldExist($locale, $productName)
    {
        $product = $this->findOneByName('product_translation', $productName);

        if (!$product->getLocale() === $locale) {
            throw new \Exception('There is no translation for product'.$productName.' in '.$locale.'locale');
        }
    }

    /**
     * @Given the following attribute translations exist:
     */
    public function theFollowingAttributeTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $attribute = $this->findOneByName('product_attribute', $data['attribute']);
            $attribute->setCurrentLocale($data['locale']);
            $attribute->setFallbackLocale($data['locale']);
            $attribute->setName($data['name']);
        }

        $manager->flush();
    }

    /**
     * @Given the following option translations exist:
     */
    public function theFollowingOptionTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $option = $this->findOneBy('product_option', ['code' => $data['option']]);
            $option->setCurrentLocale($data['locale']);
            $option->setFallbackLocale($data['locale']);
            $option->setName($data['presentation']);
        }

        $manager->flush();
    }

    /**
     * @param ProductInterface $product
     * @param array            $data
     */
    private function configureProductPricingCalculator(ProductInterface $product, array $data)
    {
        $product->getFirstVariant()->setPricingCalculator($data['pricing calculator']);

        if (!isset($data['calculator configuration']) || '' === $data['calculator configuration']) {
            throw new \InvalidArgumentException('You must set chosen calculator configuration');
        }

        $product->getFirstVariant()->setPricingConfiguration($this->getPricingCalculatorConfiguration($data));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getPricingCalculatorConfiguration(array $data)
    {
        $calculatorConfiguration = $this->getConfiguration($data['calculator configuration']);

        $finalCalculatorConfiguration = [];
        $channelRepository = $this->getRepository('channel');

        foreach ($calculatorConfiguration as $channelCode => $price) {
            $channel = $channelRepository->findOneBy(['code' => $channelCode]);

            $finalCalculatorConfiguration[$channel->getId()] = (int) round($price * 100);
        }

        return $finalCalculatorConfiguration;
    }

    /**
     * @Given product :productName has main taxon :mainTaxonName
     */
    public function productHasMainTaxon($productName, $mainTaxonName)
    {
        $manager = $this->getEntityManager();

        $product = $this->findOneByName('product', $productName);
        $mainTaxon = $this->findOneByName('taxon', $mainTaxonName);
        $product->setMainTaxon($mainTaxon);
        $manager->flush($product);
    }

    /**
     * @Given /^I delete "([^""]*)" attribute$/
     */
    public function iDeleteAttribute($attribute)
    {
        $item = $this->assertSession()->elementExists('css', sprintf('.collection-item:contains("%s")', $attribute));

        $item->clickLink('Delete');
    }

    /**
     * @Then /^I should be on the product attribute creation page for "([^"]*)" type$/
     */
    public function iShouldBeOnTheProductAttributeCreationPageForType($type)
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('product attribute creation', ['type' => $type]));
    }

    /**
     * @Given /^There is (enabled|disabled) product named "([^""]*)"$/
     */
    public function thereIsProduct($enabled, $name)
    {
        $product = $this->getRepository('product')->findOneByName($name);

        if (null === $product) {
            $product = $this->getRepository('product')->createNew();
            $product->setName($name);
            $product->getFirstVariant()->setPrice(0);
            $product->setDescription('Lorem ipsum');
        }

        $product->setEnabled('enabled' === $enabled);

        $manager = $this->getEntityManager();
        $manager->persist($product);
        $manager->flush();
    }

    /**
     * @Given /^product "([^"]*)" has been deleted$/
     */
    public function productHasBeenDeleted($productName)
    {
        $this->getSession()->visit($this->generatePageUrl('sylius_backend_product_index'));

        $tr = $this->assertSession()->elementExists('css', sprintf('table tbody tr:contains("%s")', $productName));
        $locator = 'button:contains("Delete")';
        $tr->find('css', $locator)->press();
    }

    /**
     * @When /^I click "([^"]*)" near "([^"]*)" in variant$/
     */
    public function iClickNearInVariant($button, $value)
    {
        $tr = $this->assertSession()->elementExists('css', sprintf('table#variants tbody tr:contains("%s")', $value));
        $tr->clickLink($button);
    }

    /**
     * @param string $translatedNames
     *
     * @return string
     */
    private function generateCode($name)
    {
        return strtoupper(str_replace(' ', '_', str_replace("\"", "", $name)));
    }
}
