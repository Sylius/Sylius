<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Shipping\Model\RuleInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

class ShippingContext extends DefaultContext
{
    /**
     * @Given /^the following shipping categories are configured:$/
     * @Given /^the following shipping categories exist:$/
     * @Given /^there are following shipping categories:$/
     */
    public function thereAreShippingCategories(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsShippingCategory($data['name'], $data['code'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created shipping category "([^""]*)" with code "([^""]*)"$/
     * @Given /^there is shipping category "([^""]*)" with code "([^""]*)"$/
     */
    public function thereIsShippingCategory($name, $code, $flush = true)
    {
        /* @var $category ShippingCategoryInterface */
        $category = $this->getFactory('shipping_category')->createNew();
        $category->setName($name);
        $category->setCode($code);

        $manager = $this->getEntityManager();
        $manager->persist($category);
        if ($flush) {
            $manager->flush();
        }

        return $category;
    }

    /**
     * @Given /^shipping method "([^""]*)" has following rules defined:$/
     */
    public function theShippingMethodHasFollowingRulesDefined($name, TableNode $table)
    {
        $shippingMethod = $this->findOneByName('shipping_method', $name);

        $manager = $this->getEntityManager();
        $factory = $this->getFactory('shipping_method_rule');

        foreach ($table->getHash() as $data) {
            /* @var $rule RuleInterface */
            $rule = $factory->createNew();
            $rule->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $rule->setConfiguration($this->getConfiguration($data['configuration']));

            $shippingMethod->addRule($rule);

            $manager->persist($rule);
        }

        $manager->flush();
    }

    /**
     * @Given the shipping method translations exist:
     */
    public function theShippingMethodTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $shippingMethodTranslation = $this->findOneByName('shipping_method_translation', $data['shipping method']);

            $shippingMethod = $shippingMethodTranslation->getTranslatable();
            $shippingMethod->setCurrentLocale($data['locale']);
            $shippingMethod->setFallbackLocale($data['locale']);

            $shippingMethod->setName($data['name']);
        }

        $manager->flush();
    }
}
