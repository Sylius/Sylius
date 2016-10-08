The ShippableInterface
======================

In order to handle your merchandise through the Sylius shipping engine, your models need to implement **ShippableInterface**.

Implementing the interface
--------------------------

Let's assume that you have a **Book** entity in your application.

All you need to do is to implement the simple interface, which contains few simple methods.

.. code-block:: php

    namespace Acme\Bundle\ShopBundle\Entity;

    use Sylius\Component\Shipping\Model\ShippableInterface;

    class Book implements ShippableInterface
    {
        public function getShippingWeight()
        {
            // return integer representing the object weight.
        }

        public function getShippingWidth()
        {
            // return integer representing the book width.
        }

        public function getShippingHeight()
        {
            // return integer representing the book height.
        }

        public function getShippingDepth()
        {
            // return integer representing the book depth.
        }
    }

Done! Now your **Book** model can be used in Sylius shippingation engine.
