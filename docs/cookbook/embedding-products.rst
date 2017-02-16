How to embed a list of products into a view?
============================================

Let's imagine that you would like to render **a list of 5 latest products by a chosen taxon**. Such an action can take place
on the category page. Here are the steps that you will need to take:

Create a new method for the product repository
----------------------------------------------

To cover the usecase we have imagined we will need a new method on the product repository: ``findLatestByChannelAndTaxonCode()``.

.. tip::

    First learn how to customize repositories in :doc:`the customization docs here </customization/repository>`.

The new repository method will take a channel object (retrieved from channel context), a taxon code and the count of items that you want to find.

Your extending repository class should look like that:

.. code-block:: php

    <?php

    namespace AppBundle\Repository;

    use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;
    use Sylius\Component\Core\Model\ChannelInterface;

    class ProductRepository extends BaseProductRepository
    {
        /**
         * {@inheritdoc}
         */
        public function findLatestByChannelAndTaxonCode(ChannelInterface $channel, $code, $count)
        {
            return $this->createQueryBuilder('o')
                ->innerJoin('o.channels', 'channel')
                ->addOrderBy('o.createdAt', 'desc')
                ->andWhere('o.enabled = true')
                ->andWhere('channel = :channel')
                ->innerJoin('o.taxons', 'taxon')
                ->andWhere('taxon.code = :code')
                ->setParameter('channel', $channel)
                ->setParameter('code', $code)
                ->setMaxResults($count)
                ->getQuery()
                ->getResult()
            ;
        }
    }

And should be registered in the ``app/config/config.yml`` just like that:

.. code-block:: yaml

    sylius_product:
        resources:
            product:
                classes:
                    repository: AppBundle\Repository\ProductRepository

Configure routing for the action of products rendering
------------------------------------------------------

To be able to render a partial with the retrieved products configure routing for it in the ``app/config/routing.yml``:

.. code-block:: yaml

    # app/config/routing.yml
    app_shop_partial_product_index_latest_by_taxon_code:
        path: /latest/{code}/{count} # configure a new path that has all the needed variables
        methods: [GET]
        defaults:
            _controller: sylius.controller.product:indexAction # you make a call on the Product Controller's index action
            _sylius:
                template: $template
                repository:
                    method: findLatestByChannelAndTaxonCode # here use the new repository method
                    arguments:
                        - "expr:service('sylius.context.channel').getChannel()"
                        - $code
                        - $count

Render the result of your new path in a template
------------------------------------------------

Having a new path, you can call it in a twig template. Remember that you need to have your **taxon as a variable available there**.
Render the list using a simple template first.

.. code-block:: twig

    {{ render(url('app_shop_partial_product_index_latest_by_taxon_code', {'code': taxon.code, 'count': 5, 'template': '@SyliusShop/Product/_simpleList.html.twig'})) }}

Done. In the taxon view where you have rendered the new url you will see a simple list of 5 newest products from this taxon.

Learn more
----------

* :doc:`The Customization Guide </customization/index>`
