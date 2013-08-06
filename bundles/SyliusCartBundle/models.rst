The Cart and CartItem
=====================

Here is a quick reference of what the default models can do for you.

Cart
----

You can access the cart total value using the ``->getTotal()`` method. The denormalized number of cart items is available via ``->getTotalItems()`` method.
Recalculation of totals can happen by calling ``->calculateTotal()`` method, using the simplest possible math. It will also update the item totals.
The carts have their expiration time - ``->getExpiresAt()`` returns that time and ``->incrementExpiresAt()`` sets it to +3 hours from now by default.
The collection of items (Implementing the ``Doctrine\Common\Collections\Collection`` interface) can be obtained using the ``->getItems()``.

CartItem
--------

Just like for cart, the total is available via the same method (``->getTotal()``), but the unit price is accessible using the ``->getUnitPrice()`` 
Each item also can calculate its total, using the quantity (``->getQuantity()``) and the unit price.
It also has a very important method called ``->equals(CartItemInterface $item)``, which decides whether the items are "same" or not.
If they are, it should return *true*, *false* otherwise. This is taken into account when adding item to cart.
**If the added item is equal to existing one, their quantities are summed, but no new item is added to cart**.
By default, it compares the ids, but for our example we would prefer to check the products. We can easily modify our *CartItem* entity to do that correctly.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/CartItem.php
    namespace App/AppBundle/Entity;

    use Sylius\Bundle\CartBundle\Model\CartItem as BaseCartItem;
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
