<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Association\Model\AssociationType;
use Sylius\Component\Association\Model\AssociationTypeInterface;
use Sylius\Component\Product\Model\ProductAssociation;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProductAssociationContext extends DefaultContext
{
    /**
     * @var ProductInterface
     */
    private $currentProduct;

    /**
     * @When I create :typeName association type
     */
    public function iCreateAssociationType($typeName)
    {
        $this->fillField('Name', $typeName);
        $this->pressButton('Create');
        $this->assertSession()->pageTextContains('Association type has been successfully created.');
    }

    /**
     * @Then I should be able to add :typeName associations to every product
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
        if (!$associationType instanceof AssociationTypeInterface) {
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
            $associationType = new AssociationType();
            $associationType->setName($row['name']);
            $this->getEntityManager()->persist($associationType);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @Given I want to assign new association for :productName product
     */
    public function iWantToAssignNewAssociationForProduct($productName)
    {
        $this->currentProduct = $this->getRepository('Product')->findOneBy(array('name' => $productName));
    }

    /**
     * @When I select :associatedProductName product as :associationTypeName association
     */
    public function iSelectProductAsAssociation($associatedProductName, $associationTypeName)
    {
        if (!$this->currentProduct) {
            throw new \RuntimeException('Current product have to be set first. Please run \'I want to assign new association for "<product name>" product\' first');
        }

        $associatedProduct = $this->getRepository('Product')->findOneBy(array('name' => $associatedProductName));
        $associationType = $this->getRepository('AssociationType')->findOneBy(array('name' => $associationTypeName));

        $this->currentProduct->addAssociation(new ProductAssociation($associatedProduct, $associationType));

        $this->getEntityManager()->persist($this->currentProduct);
        $this->getEntityManager()->flush();
    }

    /**
     * @Then I should see that :product is connected with :associatedProduct by :associationType association
     */
    public function iShouldSeeThatIsConnectedWithByAssociation($product, $associatedProduct, $associationType)
    {
        $product = $this->getRepository('Product')->findOneBy(array('name' => $product));
        $associatedProduct = $this->getRepository('Product')->findOneBy(array('name' => $associatedProduct));
        $associationType = $this->getRepository('AssociationType')->findOneBy(array('name' => $associationType));

        $this->getSession()->visit($this->generateUrl('sylius_backend_product_update', array('id' => $product->getId())));
        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][type]', $associationType->getId());
        $this->assertSession()->fieldValueEquals('sylius_product[associations][0][product]', $associatedProduct->getId());
    }
}
