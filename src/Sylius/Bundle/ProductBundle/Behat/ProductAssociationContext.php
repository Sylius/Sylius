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
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProductAssociationContext extends DefaultContext
{
    /**
     * @When I create :typeName association type with :code code
     */
    public function iCreateAssociationType($typeName, $code)
    {
        $this->fillField('Name', $code);
        $this->fillField('Code', $typeName);
        $this->pressButton('Create');
    }

    /**
     * @Given there are following association types:
     */
    public function thereAreFollowingAssociationTypes(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $associationType = $this->getService('sylius.factory.product_association_type')->createNew();
            $associationType->setName($row['name']);
            $associationType->setCode($row['code']);
            $this->getEntityManager()->persist($associationType);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @Given I want to create new association type
     */
    public function iWantToCreateNewAssociationType()
    {
        $this->iAmOnThePage('product association type creation');
    }
}
