SyliusCartBundle
================

A generic solution for cart system inside Symfony2 application. 

It doesn't matter if you are starting new project or you need to implement this feature for existing system - this bundle should be helpful.
Currently only the Doctrine ORM driver is implemented, so we'll use it here as example.

There are two main models inside the bundle, `Cart` and `CartItem`.
Currently the bundle requires a bit of coding from you, but we're working on simplifying the integration process.

There are also 3 main services, **Operator**, **Provider** and **ItemResolver**.
You'll get familiar with them in further parts of this documentation.

Installation
------------

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/cart-bundle:*

Adding required bundles to the kernel
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

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

Creating your entities
~~~~~~~~~~~~~~~~~~~~~~

.. note::

    We're trying to eliminate or simplify this part of setup, until that happens - you need to do this manually.

You have to create two entities, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``AppBundle`` registered under ``App\Bundle\AppBundle`` namespace.

We need two classes, *Cart* and *CartItem*.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/Cart.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Entity\Cart as BaseCart;

    class Cart extends BaseCart
    {
    }

Notice that we're using a base cart entity from the Sylius bundle.

That would be all for *Cart* model, at least for simple use cases.
Next step requires creating the item entity, let's do this now.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Entity\CartItem as BaseCartItem;

    class CartItem extends BaseCartItem
    {
    }

Now we need to define simple mapping for those entities, because they only extend the Doctrine mapped super classes.
You should create two mapping files in your ``AppBundle``, put them inside the doctrine mapping directory ``src/App/AppBundle/Resource/config/doctrine/*.orm.xml``.

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

This makes our recently created *Cart* class an entity, and adds a relation to items.
We need to take care of the opposite side of this relationship.

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

Let's assume you have *Product* entity, which represents your main merchandise in webshop.
We need to modify the *CartItem* entity and its mapping a bit, so it allows us to put product inside cart item.

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
            <many-to-one field="cart" target-entity="App\AppBundle\Entity\Cart" inversed-by="items">
                <join-column name="cart_id" referenced-column-name="id" />
            </many-to-one>
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

    use Sylius\Bundle\CartBundle\Model\CartItemRequest;
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

We must use this repository to find a product with id, given by the user via request.
This can be done in various ways, but to keep the example simple - we'll use query parameter.

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
~~~~~~~~~~~~~~~~~~~~~~~

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_cart:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        resolver: app.cart_item_resolver # The id of our newly created service.
        classes:
            cart:
                model: App\AppBundle\Entity\Cart # Our cart entity.
            item:
                model: App\AppBundle\Entity\CartItem # The item entity.

Importing routing configuration
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Import default routing from your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_cart:
        resource: @SyliusCartBundle/Resource/config/routing.yml
        prefix: /cart

Updating database schema
~~~~~~~~~~~~~~~~~~~~~~~~

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. note::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Templates
~~~~~~~~~

We think that providing a sensible default template is really difficult, especially that cart summary is not the simplest page.
This is the reason why we do not currently include any, but if you have an idea for a good starter template, let us know!

The bundle requires only the ``show.html`` template for cart summary page.
Easiest way to override the view is placing it here ``app/Resources/SyliusCartBundle/views/Cart/show.html.twig``.

.. note::

    You can use `the templates from our Sandbox app as inspiration <https://github.com/Sylius/Sylius-Sandbox/blob/master/sandbox/Resources/SyliusCartBundle/views/Cart/show.html.twig>`_.

Routing and default actions
---------------------------

Bundle provides quite simple default routing with several handy and common actions.
You can see usage guide below.

Cart summary page
~~~~~~~~~~~~~~~~~

To point user to the cart summary page, you can use the ``sylius_cart_show`` route.
It will render the page with the `cart` and `form` variables by default.

The `cart` is the current cart and `form` is the view of cart form.

Adding cart item
~~~~~~~~~~~~~~~~

In our simple example, we only need to add following link in the places where we need the "add to cart button".

.. code-block:: html

    <a href="{{ path('sylius_cart_item_add', {'productId': product.id})}}">Add product to cart</a>

Clicking this link will add the selected product to cart.

Removing item
~~~~~~~~~~~~~

On cart summary page you have access to all cart items, so another simple link will allow user to remove items from cart.

.. code-block:: html

    <a href="{{ path('sylius_cart_item_remove', {'id': item.id})}}">Remove from cart</a>

Where `item` variable represents one of `cart.items` collection item.

Clearing the whole cart
~~~~~~~~~~~~~~~~~~~~~~~

Clearing the cart is simple as clicking the following link.

.. code-block:: html

    <a href="{{ path('sylius_cart_clear')}}">Clear cart</a>

Basic cart update
~~~~~~~~~~~~~~~~~

On cart summary page, you have access to the cart form, if you want to save it, simply submit the form
with following action.

.. code-block:: html

    <form action="{{ path('sylius_cart_save')}}" method="post">Clear cart</a>

You cart will be validated and saved if everything is alright.

Using the services
------------------

When using the bundle, you have access to several handy services.
You can use them to manipulate and manage the cart.

Managers and Repositories
~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    Sylius uses ``Doctrine\Common\Persistence`` interfaces.

You have access to following services which are used to manage and retrieve resources.

This set of default services is shared across almost all Sylius bundles, but this is just a convention.
You're interacting with them like you usually do with own entities in your project.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        // ObjectManager which is capable of managing the Cart resource.
        // For *doctrine/orm* driver it will be EntityManager.
        $this->get('sylius_cart.manager.cart'); 

        // ObjectRepository for the Cart resource, it extends the base EntityRepository.
        // You can use it like usual entity repository in project.
        $this->get('sylius_cart.repository.cart'); 

        // Same pair for CartItem resource.
        $this->get('sylius_cart.manager.item');
        $this->get('sylius_cart.repository.item');

        // Those repositories have some handy default methods, for example...
        $item = $itemRepository->createNew();
    }

Provider, Operator and Resolver
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are also 3 more services for you.

You use provider to obtain the current user cart, if there is none, a new one is created and saved.
The ``->setCart()`` method also allows you to replace the current cart.
``->abandonCart()`` is resetting the current cart, a new one will be created on next ``->getCart()`` call.
This is useful, for example, when after completing an order you want to start with a brand new and clean cart.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        $provider = $this->get('sylius_cart.provider'); // Implements the CartProviderInterface.

        $currentCart = $provider->getCart();
        $provider->setCart($customCart);
        $provider->abandonCart();
    }

Operator is used to perform basic actions on the given cart.
It is available as service, you can override its class or even whole service to modify the default logic.

.. code-block:: php

    <?php

    // ...
    public function addItemAction(Request $request)
    {
        // ...

        $provider = $this->get('sylius_cart.provider');
        $operator = $this->get('sylius_cart.operator'); // Implements the CartOperatorInterface.

        $cart = $provider->getCart();

        $operator
            ->addItem($cart, $newItem)
            ->removeItem($cart, $existingItem)
            ->refresh($cart) // Forces cart to refresh all its data, recalculate totals...
            ->save($cart) // Save and flush the cart.
        ;

        $operator->clear($cart); // Clears the cart.
    }

The resolver is used to create a new item based on user request.

.. code-block:: php

    <?php

    // ...
    public function addItemAction(Request $request)
    {
        $resolver = $this->get('sylius_cart.resolver');
        $item = $this->resolve($this->createNew(), $request);
    }

.. note::

    A more advanced example of resolver implementation is available `in Sylius Sandbox application on GitHub <https://github.com/Sylius/Sylius-Sandbox/blob/master/src/Sylius/Bundle/SandboxBundle/Resolver/ItemResolver.php>`_.

In templates
------------

When using Twig as your template engine, you have access to 2 handy functions.

The ``sylius_cart_get`` function uses provider to get the current cart.

.. code-block:: jinja

    {% set cart = sylius_cart_get() %}

    Current cart totals: {{ cart.total }} for {{ cart.totalItems }} items!

The ``sylius_cart_form`` returns the form view for the CartItem form. It allows you to easily build more complex actions for
adding items to cart. In this simple example we allow to provide the quantity of item. You'll need to process this form in your resolver.

.. code-block:: jinja

    {% set form = sylius_cart_form({'product': product}) %} {# You can pass options as an argument. #}

    <form action="{{ path('sylius_cart_item_add', {'productId': product.id}) }}" method="post">
        {{ form_row(form.quantity)}}
        <input type="submit" value="Add to cart">
    </form>

.. note::

     An example with multiple variants of this form `can be found in Sylius Sandbox app <https://github.com/Sylius/Sylius-Sandbox/blob/master/src/Sylius/Bundle/SandboxBundle/Form/Type/CartItemType.php>`_.
     It allows for selecting a variation/options/quantity of product. It also adapts to the product type.

The Cart and CartItem
---------------------

Here is a quick reference of what the default models can do for you.

Cart
~~~~

You can access the cart total value using the ``->getTotal()`` method. The denormalized number of cart items is available via ``->getTotalItems()`` method.
Recalculation of totals can happen by calling ``->calculateTotal()`` method, using the simplest possible math. It will also update the item totals.
The carts have their expiration time - ``->getExpiresAt()`` returns that time and ``->incrementExpiresAt()`` sets it to +3 hours from now by default.
The collection of items (Implementing the ``Doctrine\Common\Collections`` interface) can be obtained using the ``->getItems()``.

CartItem
~~~~~~~~

Just like for cart, the total is available via the same method, but the unit price is accessible using the ``->getUnitPrice()`` 
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.
It also has a very important method called ``->equals(CartItemInterface $item)``, which decides whether the items are "same" or not.
If they are, it should return *true*, *false* otherwise. This is taken into account when adding item to cart.
**If the added item is equal to existing one, their quantities are summed, but no new item is added to cart**.
By default, it compares the ids, but for our example we would prefer to check the products. We can easily modify our *CartItem* entity to do that correctly.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Entity\CartItem as BaseCartItem;
    use Sylius\Bundle\CartBundle\Model\CartItemInterface;

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

        public function equals(CartItemInterface $item)
        {
            return $this->product === $item->getProduct();
        }
    }

If user tries to add same product twice or more, it will just sum the quantities, instead of adding duplicates to cart.

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
