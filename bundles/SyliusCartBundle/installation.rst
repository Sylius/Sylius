Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/cart-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

Creating your entities
----------------------

You have to create your **CartItem** entity, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\Bundle\AppBundle`` namespace.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\CartBundle\Entity\CartItem as BaseCartItem;

    class CartItem extends BaseCartItem
    {
    }

Now we need to define simple mapping for this entity, because it only extends the Doctrine mapped super class.
You should create a mapping file in your ``AppBundle``, put it inside the doctrine mapping directory ``src/App/AppBundle/Resources/config/doctrine/CartItem.orm.xml``.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                                 http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\CartItem" table="app_cart_item">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>
        </entity>

    </doctrine-mapping>

Mapping *id* is enough, because the relation between **Cart** and **CartItem** has been already defined in superclass metadata.

Let's assume you have *Product* entity, which represents your main merchandise in webshop.

.. note::

    Please remember that you can use anything else, *Product* here is just an obvious example, but it will work in similar way with other entities.

We need to modify the *CartItem* entity and its mapping a bit, so it allows us to put product inside cart item.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App\AppBundle\Entity;

    use Sylius\Bundle\CartBundle\Entity\CartItem as BaseCartItem;

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

We added a "product" property, simple getter and setter.
We have to also map the *Product* to *CartItem*, let's create this relation in mapping files.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                             xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                                 http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\CartItem" table="app_cart_item">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>
            <many-to-one field="product" target-entity="App\AppBundle\Entity\Product">
                <join-column name="product_id" referenced-column-name="id" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

And that would be all about entities. 

Now we need to create really simple service.
The **ItemResolver**, which will be used by controller to resolve the new cart item - based on user request information.
Its only requirement is to implement ``Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface``.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemInterface;
    use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
    use Symfony\Component\HttpFoundation\Request;

    class ItemResolver implements ItemResolverInterface
    {
        public function resolve(CartItemInterface $item, Request $request)
        {
        }
    }

The class is in place, well done.

We need to do some more coding, so the service is actually doing its job.
In our example we want to put *Product* in our cart, so we should
inject the entity manager to our resolver service.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemInterface;
    use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
    use Symfony\Component\HttpFoundation\Request;

    class ItemResolver implements ItemResolverInterface
    {
        private $entityManager;

        public function __construct(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function resolve(CartItemInterface $item, Request $request)
        {
        }

        private function getProductRepository()
        {
            return $this->entityManager->getRepository('AppBundle:Product');
        }
    }

We also added a simple method ``getProductRepository()`` to keep the resolving code cleaner.

We must use this repository to find a product with id, given by the user via request.
This can be done in various ways, but to keep the example simple - we'll use query parameter.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemInterface;
    use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
    use Sylius\Bundle\CartBundle\Resolver\ItemResolvingException;
    use Symfony\Component\HttpFoundation\Request;

    class ItemResolver implements ItemResolverInterface
    {
        private $entityManager;

        public function __construct(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        public function resolve(CartItemInterface $item, Request $request)
        {
            $productId = $request->query->get('productId');

            // If no product id given, or product not found, we throw exception with nice message.
            if (!$productId || !$product = $this->getProductRepository()->find($productId)) {
                throw new ItemResolvingException('Requested product was not found');
            }

            // Assign the product to the item and define the unit price.
            $item->setProduct($product);
            $item->setUnitPrice($product->getPrice());

            // Everything went fine, return the item.
            return $item;
        }

        private function getProductRepository()
        {
            return $this->entityManager->getRepository('AppBundle:Product');
        }
    }

Register our brand new service in container. We'll use XML as example, but you are free to pick any other format.

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

Bundle requires also simple configuration...

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_cart:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        resolver: app.cart_item_resolver # The id of our newly created service.
        classes:
            item:
                model: App\AppBundle\Entity\CartItem # The item entity.

Importing routing configuration
-------------------------------

Import default routing from your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_cart:
        resource: @SyliusCartBundle/Resources/config/routing.yml
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

We think that providing a sensible default template is really difficult, especially that cart summary is not the simplest page.
This is the reason why we do not currently include any, but if you have an idea for a good starter template, let us know!

The bundle requires only the ``show.html`` template for cart summary page.
Easiest way to override the view is placing it here ``app/Resources/SyliusCartBundle/views/Cart/show.html.twig``.

.. note::

    You can use `the templates from our Sandbox app as inspiration <https://github.com/Sylius/Sylius-Sandbox/blob/master/sandbox/Resources/SyliusCartBundle/views/Cart/show.html.twig>`_.
