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
use Sylius\Component\Product\Model\AssociationType;
use Sylius\Component\Product\Model\ProductAssociation;

class ProductAssociationContext extends DefaultContext
{
    private $currentProduct;

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
     * @Then I should be able to add :arg1 associations to every product
     */
    public function iShouldBeAbleToAddAssociationsToEveryProduct($typeName)
    {
        $associatedProduct = $this
            ->getRepository('Product')
            ->createQueryBuilder('p')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
       ;
        $associationType = $this->getRepository('AssociationType')->findOneBy(array('name' => $typeName));
        if (!$associationType) {
            throw new \InvalidArgumentException(sprintf('Association Type %s does not exists and it should', $typeName));
        }

        foreach ($this->getRepository('Product')->findAll() as $product) {
            $productAssociation = new ProductAssociation($associatedProduct, $associationType);
            $product->addAssociation($productAssociation);
            $this->getEntityManager()->persist($productAssociation);
        }
        $this->getEntityManager()->flush();

        $this->getSession()->visit($this->generatePageUrl('product index'));
        $this->getSession()->getPage()->clickLink('edit');
        $this->assertSession()->pageTextContains('Editing product');

        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][type]', $associationType->getId());
        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][product]', $associatedProduct->getId());
    }

    /**
     * @Given there are following association types:
     */
    public function thereAreFollowingAssociationTypes(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $associationType = new AssociationType($row['name']);
            $this->getEntityManager()->persist($associationType);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @Given I want to assign new association for :arg1 product
     */
    public function iWantToAssignNewAssociationForProduct($productName)
    {
        $this->currentProduct = $this->getRepository('Product')->findOneBy(['name' => $productName]);
    }

    /**
     * @When I select :arg1 product as :arg2 association
     */
    public function iSelectProductAsAssociation($associatedProductName, $associationTypeName)
    {
        if (!$this->currentProduct) {
            throw new \RuntimeException('Current product have to be set first. Please run \'I want to assign new association for "<product name>" product\' first');
        }

        $associatedProduct = $this->getRepository('Product')->findOneBy(['name' => $associatedProductName]);
        $associationType = $this->getRepository('AssociationType')->findOneBy(['name' => $associationTypeName]);

        $this->currentProduct->addAssociation(new ProductAssociation($associatedProduct, $associationType));

        $this->getEntityManager()->persist($this->currentProduct);
        $this->getEntityManager()->flush();
    }

    /**
     * @Then I should see that :arg1 is connected with :arg2 by :arg3 association
     */
    public function iShouldSeeThatIsConnectedWithByAssociation($productName, $associatedProductName, $associationTypeName)
    {
        $product = $this->getRepository('Product')->findOneBy(array('name' => $productName));
        $associatedProduct = $this->getRepository('Product')->findOneBy(array('name' => $associatedProductName));
        $associationType = $this->getRepository('AssociationType')->findOneBy(array('name' => $associationTypeName));

        $this->getSession()->visit($this->generateUrl('sylius_backend_product_update', array('id' => $product->getId())));
        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][type]', $associationType->getId());
        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][product]', $associatedProduct->getId());
    }
}
