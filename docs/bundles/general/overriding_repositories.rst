Overriding Repositories
=======================

Overriding a Sylius model repository involves extending the base class and configuring it inside the bundle configuration.

Extending base Repository
-------------------------

Sylius is using both custom and default Doctrine repositories and often you'll need to add your own methods. Let's assume you want to find all orders for a given customer.

Firstly, you need to create your own repository class

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Repository/OrderRepository.php

    namespace Acme\ShopBundle\Repository;

    use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

    class OrderRepository extends EntityRepository
    {
        public function findByCustomer(Customer $customer)
        {
            return $this
                ->createQueryBuilder('o')
                ->join('o.billingAddress', 'billingAddress')
                ->join('o.shippingAddress', 'shippingAddress')
                ->join('o.customer', 'customer')
                ->where('o.customer = :customer')
                ->setParameter('customer', $customer)
                ->getQuery()
                ->getResult()
            ;
        }
    }

Secondly, need to configure your repository class in ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    sylius_order:
        driver: doctrine/orm
        classes:
            order:
                repository: Acme\ShopBundle\Repository\OrderRepository

That's it! Now ``sylius.repository.order`` is using your new class.

.. code-block:: php

    <?php

    public function ordersAction()
    {
        $customer = // Obtain customer instance.
        $repository = $this->container->get('sylius.repository.order');

        $orders = $repository->findByCustomer($customer);
    }

What has happened?

* Parameter ``sylius.repository.order.class`` contains ``Acme\\ShopBundle\\Repository\\OrderRepository``.
* Repository service ``sylius.repository.order`` is using your new class.
