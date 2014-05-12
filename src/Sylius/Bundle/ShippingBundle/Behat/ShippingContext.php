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
            $this->thereIsShippingCategory($data['name'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created shipping category "([^""]*)"$/
     * @Given /^there is shipping category "([^""]*)"$/
     */
    public function thereIsShippingCategory($name, $flush = true)
    {
        /* @var $category ShippingCategoryInterface */
        $category = $this->getRepository('shipping_category')->createNew();
        $category->setName($name);

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
        $repository = $this->getRepository('shipping_method_rule');

        foreach ($table->getHash() as $data) {
            /* @var $rule RuleInterface */
            $rule = $repository->createNew();
            $rule->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $rule->setConfiguration($this->getConfiguration($data['configuration']));

            $shippingMethod->addRule($rule);

            $manager->persist($rule);
        }

        $manager->flush();
    }
}
