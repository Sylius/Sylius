---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Product Associations

Associations of products can be used as a marketing tool for suggesting to your customers, what products to buy together with the one they are currently considering. Associations can increase your shop’s efficiency. You choose what strategy you prefer. They are fully configurable.

## Association Types

The type of association can be different. If you sell food you can suggest inspiring ingredients, if you sell products for automotive you can suggest buying some tools that may be useful for a home car mechanic. Exemplary association types can be: `up-sell`, `cross-sell`, `accessories`, `alternatives` and whatever you imagine.

### How to create a new Association Type?

Create a new Association Type using a dedicated factory. Give the association a `code` and a `name` to easily recognize the type.

```php
/** @var ProductAssociationTypeInterface $associationType */
$associationType = $this->container->get('sylius.factory.product_association_type')->createNew();

$associationType->setCode('accessories');
$associationType->setName('Accessories');
```

To have the new association type in the system add it to the repository.

```php
$this->container->get('sylius.repository.product_association_type')->add($associationType);
```

## How to add a new Association to a Product?

Find in your system a product to which you would like to add an association. We will use a Go Pro camera as an example.

```php
$product = $this->container->get('sylius.repository.product')->findOneBy(['code' => 'go-pro-camera']);
```

Next, create a new Association which will connect our camera with its accessories. Such an association needs the AssociationType we have created in the previous step above.

```php
/** @var ProductAssociationInterface $association */
$association = $this->container->get('sylius.factory.product_association')->createNew();

/** @var ProductAssociationTypeInterface $associationType */
$associationType = $this->container->get('sylius.repository.product_association_type')->findOneBy(['code' => 'accessories']);

$association->setType($associationType);
```

Let’s add all products from a certain taxon to the association we have created. To do that find a desired taxon by code and get all its products. Perfect accessories for a camera will be SD cards.

```php
/** @var TaxonInterface $taxon */
$taxon = $this->container->get('sylius.repository.taxon')->findOneBy(['code' => 'sd-cards']);

$associatedProducts = $this->container->get('sylius.repository.product')->findByTaxon($taxon);
```

Having a collection of products from the SD cards taxon iterate over them and add them one by one to the association.

```php
foreach ($associatedProducts as $associatedProduct) {
    $association->addAssociatedProduct($associatedProduct);
}
```

Finally, add the created association with SD cards to our Go Pro camera product.

```php
$product->addAssociation($association);
```

And to save everything in the database you need to add the created association to the repository.

```php
$this->container->get('sylius.repository.product_association')->add($association);
```

In the previous example we used a custom query in the product repository, here is the implementation:

```php
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

class ProductRepository extends BaseProductRepository
{
    public function findByTaxon(Taxon $taxon): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.productTaxons', 'pt')
            ->where('pt.taxon = :taxon')
            ->setParameter('taxon', $taxon)
            ->getQuery()
            ->getResult()
         ;
    }
}
```
