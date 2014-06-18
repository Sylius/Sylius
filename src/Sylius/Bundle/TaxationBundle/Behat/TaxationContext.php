<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

class TaxationContext extends DefaultContext
{
    /**
     * @Given /^there are following tax categories:$/
     * @Given /^the following tax categories exist:$/
     */
    public function thereAreTaxCategories(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxCategory($data['name'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^There is tax category "([^""]*)"$/
     * @Given /^I created tax category "([^""]*)"$/
     */
    public function thereIsTaxCategory($name, $flush = true)
    {
        $category = $this->getRepository('tax_category')->createNew();
        $category->setName($name);

        $manager = $this->getEntityManager();
        $manager->persist($category);
        if ($flush) {
            $manager->flush();
        }

        return $category;
    }

    /**
     * @Given /^the default tax zone is "([^""]*)"$/
     */
    public function theDefaultTaxZoneIs($zone)
    {
        /* @var $settingsManager SettingsManagerInterface */
        $settingsManager = $this->getService('sylius.settings.manager');
        $settings = $settingsManager->loadSettings('taxation');
        $settings->set('default_tax_zone', $this->findOneByName('zone', $zone));

        $settingsManager->saveSettings('taxation', $settings);
    }
}
