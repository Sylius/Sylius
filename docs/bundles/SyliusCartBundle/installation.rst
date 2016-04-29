Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/cart-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/cart-bundle

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.  If you're not using
any other Sylius bundles, you will also need to add the following bundles and
their dependencies to the kernel:

    - `SyliusResourceBundle`
    - `SyliusMoneyBundle`
    - `SyliusOrderBundle`

Don't worry, everything was automatically installed via Composer.

.. note::

    Please register the bundle **before** *DoctrineBundle*. This is important
    as we use listeners which have to be processed first. It is generally a
    good idea to place all of the Sylius bundles at the beginning of the
    bundles list, as it is done in the `Sylius-Standard` project.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new Sylius\Bundle\OrderBundle\SyliusOrderBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
        );
    }

Creating your entities
----------------------

This is no longer a required step in the latest version of the
`SyliusCartBundle`, and if you are happy with the default implementation (which
is ``Sylius\Bundle\CartBundle\Model\CartItem``), you can just skip to the next
section.

You can create your **CartItem** entity, living inside your application code.
We think that **keeping the application-specific and simple bundle structure** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\AppBundle`` namespace.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App\AppBundle\Entity;

    use Sylius\Component\Cart\Model\CartItem as BaseCartItem;

    class CartItem extends BaseCartItem
    {
    }

Now we need to define a simple mapping for this entity to map its fields.
You should create a mapping file in your ``AppBundle``, put it inside the doctrine mapping directory ``src/App/AppBundle/Resources/config/doctrine/CartItem.orm.xml``.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                                 http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\CartItem" table="app_cart_item">
        </entity>

    </doctrine-mapping>

You do **not** have to map the *ID* field because it is already mapped in the
``Sylius\Component\Cart\Model\CartItem`` class, together with the relation
between **Cart** and **CartItem**.

Let's assume you have a *Product* entity, which represents your main merchandise within your webshop.

.. note::

    Please remember that you can use anything else, *Product* here is just an obvious example, but it will work in a similar way with other entities.

We need to modify the *CartItem* entity and its mapping a bit, so it allows us to put a product inside the cart item.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App\AppBundle\Entity;

    use Sylius\Component\Cart\Model\CartItem as BaseCartItem;

    class CartItem extends BaseCartItem
    {
        private $product;

        public function getProduct()
        {
            return $this->product;
        }

        public function setProduct(Product $product)
        {
            $this->product = $product;
        }
    }

We added a "product" property, and a simple getter and setter.
We have to also map the *Product* to *CartItem*, let's create this relation in mapping files.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                                 http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\CartItem" table="app_cart_item">
            <many-to-one field="product" target-entity="App\AppBundle\Entity\Product">
                <join-column name="product_id" referenced-column-name="id" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Similarly, you can create a custom entity for orders. The class that you need
to extend is ``Sylius\Component\Cart\Model\Cart``. Carts and Orders in
Sylius are in fact the same thing. Do not forget to create the mapping file.
But, again, do not put a mapping for the *ID* field — it is already mapped in
the parent class.

And that would be all about entities. Now we need to create a really simple service.

Creating ItemResolver service
-----------------------------

The **ItemResolver** will be used by the controller to resolve the new cart item - based on a user request information.
Its only requirement is to implement ``Sylius\Component\Cart\Resolver\ItemResolverInterface``.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Component\Cart\Model\CartItemInterface;
    use Sylius\Component\Cart\Resolver\ItemResolverInterface;

    class ItemResolver implements ItemResolverInterface
    {
        public function resolve(CartItemInterface $item, $request)
        {
        }
    }

The class is in place, well done.

We need to do some more coding, so the service is actually doing its job.
In our example we want to put *Product* in our cart, so we should
inject the entity manager into our resolver service.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Component\Cart\Model\CartItemInterface;
    use Sylius\Component\Cart\Resolver\ItemResolverInterface;
    use Doctrine\ORM\EntityManager;

    class ItemResolver implements ItemResolverInterface
    {
        private $entityManager;

        public function __construct(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function resolve(CartItemInterface $item, $request)
        {
        }

        private function getProductRepository()
        {
            return $this->entityManager->getRepository('AppBundle:Product');
        }
    }

We also added a simple method ``getProductRepository()`` to keep the resolving code cleaner.

We must use this repository to find a product with `id`, given by the user via the request.
This can be done in various ways, but to keep the example simple - we'll use a query parameter.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Component\Cart\Model\CartItemInterface;
    use Sylius\Component\Cart\Resolver\ItemResolverInterface;
    use Sylius\Component\Cart\Resolver\ItemResolvingException;
    use Doctrine\ORM\EntityManager;

    class ItemResolver implements ItemResolverInterface
    {
        private $entityManager;

        public function __construct(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function resolve(CartItemInterface $item, $request)
        {
            $productId = $request->query->get('productId');

            // If no product id given, or product not found, we throw exception with nice message.
            if (!$productId || !$product = $this->getProductRepository()->find($productId)) {
                throw new ItemResolvingException('Requested product was not found');
            }

            // Assign the product to the item and define the unit price.
            $item->setVariant($product);
            $item->setUnitPrice($product->getPrice());

            // Everything went fine, return the item.
            return $item;
        }

        private function getProductRepository()
        {
            return $this->entityManager->getRepository('AppBundle:Product');
        }
    }

.. note::

    Please remember that **item accepts only integers as price and quantity**.

Register our brand new service in the container. We'll use XML as an example, but you are free to pick any other format.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="app.cart_item_resolver" class="App\AppBundle\Cart\ItemResolver">
                <argument type="service" id="doctrine.orm.entity_manager" />
            </service>
        </services>
    </container>

The bundle requires also a simple configuration...

Container configuration
-----------------------

Put this minimal configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_cart:
        resolver: app.cart_item_resolver # The id of our newly created service.
        classes: ~ # This key can be empty but it must be present in the configuration.

    sylius_order:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.

    sylius_money: ~


**Or**, if you have created any custom entities, use this:

.. code-block:: yaml

    sylius_cart:
        resolver: app.cart_item_resolver # The id of our newly created service.
        classes: ~ # This key can be empty but it must be present in the configuration.

    sylius_order:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        classes:
            order:
                model: App\AppBundle\Entity\Cart # If you have created a custom Cart entity.
            order_item:
                model: App\AppBundle\Entity\CartItem # If you have created a custom CartItem entity.

    sylius_money: ~

Importing routing configuration
-------------------------------

Import the default routing from your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_cart:
        resource: "@SyliusCartBundle/Resources/config/routing.yml"
        prefix: /cart

Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Templates
---------

We think that providing a sensible default template is really difficult, especially when a cart summary is not the simplest page.
This is the reason why we do not currently include any, but if you have an idea for a good starter template, let us know!

The bundle requires only the ``summary.html.twig`` template for cart summary page.
The easiest way to override the view is by placing it here ``app/Resources/SyliusCartBundle/views/Cart/summary.html.twig``.

.. note::

    You can use `the templates from our Sylius app as inspiration <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/WebBundle/Resources/views/Frontend/Cart/summary.html.twig>`_.
