SyliusCartBundle
================

A generic solution for building carts inside Symfony2 applications, it does not matter if you are
starting new project or you need to implement this feature for existing system - this bundle should be helpful.
Currently only the Doctrine ORM driver is implemented, so we'll use it here as example.

There are two main models inside the bundle, `Cart` and `CartItem`.
The second one will be the most interesting for us, as the Cart is pretty sensible default.
Currently the bundle requires a bit of coding from you, but we're working on simplifying the integration process.

Installation
------------

We assume you're familiar with `Composer <http://packagist.org>`_.

Use this command to add it to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/cart-bundle:*

Adding required bundles to kernel
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now you need to enable the bundle inside kernel.
If you're not using any other Sylius bundles, you also need to add `SyliusResourceBundle` and its dependencies to kernel.
Do not worry, it was automatically installed for you by Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\CartBundle\SyliusCartBundle(),
        );
    }

Creating your own entities
~~~~~~~~~~~~~~~~~~~~~~~~~~

Now is the part we're trying to eliminate, but for now - you need to do this manually.
You need to create two entities in your application namespace, it doesn't matter where you'll put it.
We think that keeping the app-specific bundle structure as simple as its possible is a good practice, so
let's assume you have your *AppBundle* registered under ``App\Bundle\AppBundle`` namespace.

Now you have to create two basic classes, *Cart* and *CartItem* entities.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Cart.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Entity\Cart as BaseCart;

    class Cart extends BaseCart
    {
    }

Notice that we're using a base cart entity from the Sylius bundle.
And that would be it for *Cart* model, at least for simple usage.
Next step is creating the item entity, let's do it now.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Entity\CartItem as BaseCartItem;

    class CartItem extends BaseCartItem
    {
    }

That's good start!
Now we need to define simple mapping for those entities, because they extend only Doctrine mapped superclasses.
You should create two mapping files in your *AppBundle*.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="App\AppBundle\Entity\Cart" table="app_cart">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>
            <one-to-many field="items" target-entity="App\AppBundle\Entity\CartItem" mapped-by="cart">
                <cascade>
                    <cascade-all/>
                </cascade>
            </one-to-many>
        </entity>

    </doctrine-mapping>

This makes our recently created *Cart* class an entity, and adds a relation to items. Now we need to
take care of the opposite side of this relationship.

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
            <many-to-one field="cart" target-entity="App\AppBundle\Entity\Cart" inversed-by="items">
                <join-column name="cart_id" referenced-column-name="id" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Great! But whats an cart item without some kind of "product" or any other thing you could put in cart?!
Let's assume you have another *Product* entity, which represents your main merchandise in webshop.
We need to modify the *CartItem* entity and its mapping a bit.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App/AppBundle/Entity;

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
Last to do in entities, is to map the *Product* to *CartItem*.

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
            <many-to-one field="cart" target-entity="App\AppBundle\Entity\Cart" inversed-by="items">
                <join-column name="cart_id" referenced-column-name="id" />
            </many-to-one>
            <many-to-one field="product" target-entity="App\AppBundle\Entity\Product">
                <join-column name="product_id" referenced-column-name="id" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

And that would be all about entities. Now we need to create really simple service.
The **ItemResolver**, which will be used in controller to resolve the new cart item based on user request.
It's only requirement is to implement ``Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface``.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemRequest;
    use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
    use Symfony\Component\HttpFoundation\Request;

    class ItemResolver implements ItemResolverInterface
    {
        public function resolve(CartItemInterface $item, Request $request)
        {
        }
    }

The class is in place, well done. Now we need to do some more coding, so the service is actually doing something.
Then we can register it in container. In our example we want to put *Product* in our cart, so let's
inject the entity manager to our resolver service.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemRequest;
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
Now, last thing to do is using this repository to find a product with id passed by user via request.
This can be done in very different ways, but to keep it simple we'll use query parameter.

.. code-block:: php

    <?php

    // src/App/AppBundle/Cart/ItemResolver.php
    namespace App\AppBundle\Cart;

    use Sylius\Bundle\CartBundle\Model\CartItemRequest;
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
            $productId = $request->query->get('productId');
            
            // If no product id given, or product not found, we return false to display an error.
            if (!$productId || !$product = $this->getProductRepository()->find($productId)) {
                return false;
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

Let's register our brand new service in container. We'll use XML as example but you could use any other format.

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

This would be it for coding, now some configuration...

Container configuration
~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    sylius_cart:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        resolver: app.cart_item_resolver # The id of our newly created service.
        classes:
            cart:
                model: App\AppBundle\Entity\Cart
            item:
                model: App\AppBundle\Entity\CartItem

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: yaml

    sylius_cart:
        resource: @SyliusCartBundle/Resource/config/routing.yml
        prefix: /cart

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

Remember to update your database schema!

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

This should be done only in dev environment, we recommend using Doctrine migrations, to safely update your schema.

Templates
~~~~~~~~~

We think that providing a sensible default template is really difficult, especially that cart summary is not the simplest page.
This is the reason why we do not currently provide them, but if you have an idea for a good starter template, let us know!
Or even better, open a Pull Request on GitHub, all contributions are welcome!

The bundle requires only the ``show.html`` template for cart summary page.
Easiest way to override is to put it here *app/Resources/SyliusCartBundle/views/Cart/show.html.twig*.

.. info::

    You can use `the templates from our Sandbox app as inspiration <https://github.com/Sylius/Sylius-Sandbox/blob/master/sandbox/Resources/SyliusCartBundle/views/Cart/show.html.twig>`_.


Usage guide
-----------

If the bundle is installed and configured, we're ready to go!
To point user to the cart summary page, you can use the ``sylius_cart_show`` route.
But your cart is empty yeah? Let's put some product there.
In our simple example, we would only need to put following link on the product page, list or anywhere you want.

.. code-block:: html

    <a href="{{ path('sylius_cart_item_add', {'productId': product.id})}}">Add product to cart</a>

Clicking this link will add the selected product to cart, simple!
But what if you do not like the product and want to remove it?
On cart summary page you have access to all cart items, so another simple link will do the job.

.. code-block:: html

    <a href="{{ path('sylius_cart_item_remove', {'id': item.id})}}">Remove from cart</a>

Where `item` variable represents one of `cart.items` collection item.
Clearing the cart is even simpler.

.. code-block:: html

    <a href="{{ path('sylius_cart_clear')}}">Clear cart</a>

On cart summary page, you have also access to the cart form, if you want to save it, simply submit the form
with following address.

.. code-block:: html

    <form action="{{ path('sylius_cart_save')}}" method="post">Clear cart</a>

You cart will be validated and saved if everything is alright.

When using the bundle, you have access to several handy services.

.. node::

    This part is not written yet.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_cart:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        resolver: ~ # Service id of cart item resolver.
        operator: sylius_cart.operator.default # Cart operator service id.
        provider: sylius_cart.provider.default # Cart provider service id.
        storage: sylius_cart.storage.session # The id of cart storage for default provider.
        classes:
            cart:
                model: ~ # The cart model class.
                controller: Sylius\Bundle\CartBundle\Controller\CartController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\CartBundle\Form\Type\CartType # The form type name to use.
            item:
                model: ~ # The cart item model class.
                controller: Sylius\Bundle\CartBundle\Controller\CartItemController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\CartBundle\Form\Type\CartItemType # The form type class name to use.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusCartBundle/issues>`_.
If you have found bug, please create an issue.
