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

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class ProductAssociationContext extends DefaultContext
{
    /**
     * @When I create :arg1 association type
     */
    public function iCreateAssociationType($typeName)
    {
        $this->fillField('Name', $typeName);
        $this->pressButton('Create');
        $this->assertSession()->pageTextContains('Association type has been successfully created.');
    }

    /**
     * @Then I should be able to add :arg1 associations to any product
     */
    public function iShouldBeAbleToAddAssociationsToAnyProduct($typeName)
    {
        $this->getSession()->visit($this->generatePageUrl('product index'));
        $this->getSession()->getPage()->clickLink('edit');
        $this->assertSession()->pageTextContains('Editing product');

        throw new \Exception();
        //$this->assertSession()->elementContains('css', '')
    }

    /**
     * @Given there are following association types:
     */
    public function thereAreFollowingAssociationTypes(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Given I want to assign new association for :arg1 product
     */
    public function iWantToAssignNewAssociationForProduct($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I select :arg1 product as :arg2 association
     */
    public function iSelectProductAsAssociation($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then I should see that :arg1 is connected with :arg2 by :arg3 association
     */
    public function iShouldSeeThatIsConnectedWithByAssociation($arg1, $arg2, $arg3)
    {
        throw new PendingException();
    }
}
