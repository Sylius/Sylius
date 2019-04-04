Customizing Repositories
========================

.. warning::
    In **Sylius** we are using both default Doctrine repositories and the custom ones.
    Often you will be needing to add your very own methods to them. You need to check before which repository is your resource using.

Why would you customize a Repository?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Different sets of different resources can be obtained in various scenarios in your application.
You may need for instance:

    * finding Orders by a Customer and a chosen Product
    * finding Products by a Taxon
    * finding Comments by a Customer

How to customize a Repository?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would want to find products that you are running out of in the inventory.

**1.** Create your own repository class under the ``App\Repository`` namespace.
Remember that it has to extend a proper base class. How can you check that?

For the ``ProductRepository`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.repository.product

As a result you will get the ``Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository`` - this is the class that you need to be extending.

.. code-block:: php

    <?php

    namespace App\Repository;

    use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepository;

    class ProductRepository extends BaseProductRepository
    {
        public function findByOnHand(int $limit = 8): array
        {
            return $this->createQueryBuilder('o')
                ->addSelect('variant')
                ->addSelect('translation')
                ->leftJoin('o.variants', 'variant')
                ->leftJoin('o.translations', 'translation')
                ->addOrderBy('variant.onHand', 'ASC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult()
            ;
        }
    }

We are using the `Query Builder`_ in the Repositories.
As we are selecting Products we need to have a join to translations, because they are a translatable resource. Without it in the query results we wouldn't have a name to be displayed.

We are sorting the results by the count of how many products are still available on hand, which is saved on the ``onHand`` field on the specific ``variant`` of each product.
Then we are limiting the query to 8 by default, to get only 8 products that are low in stock.

**2.** In order to use your repository you need to configure it in the ``config/packages/_sylius.yaml``.
As You see in ``_Sylius.yaml`` You already have basic configuration, now You just need add Yours repository and override resourceRepository

.. code-block:: yaml

    sylius_product:
    resources:
        product:
            classes:
                model: App\Entity\Product\Product
                repository: App\Repository\ProductRepository # you need add yours repository to config file
            translation:
                classes:
                    model: App\Entity\Product\ProductTranslation

**3.** After configuring the ``sylius.repository.product`` service has your ``findByOnHand()`` method available.
You can form now on use your method in any **Controller**, or files which use ``sylius.repository.product``.
For example you can configure new ``route``

.. code-block:: yaml

    app_admin_custom_product_index:
    path: /admin/custom-products/
    methods: [GET]
    defaults:
        _controller: sylius.controller.product:indexAction
        _sylius:
            template: '@SyliusShop/Product/_horizontalList.html.twig'
            repository:
                method: findAllByOnHand
                arguments: [2]
            criteria: false
        paginate: false
        limit: 100

And use it in override mainpage:

.. code-block:: php

{% extends '@SyliusShop/layout.html.twig' %}

{% block content %}
    <h2 class="ui horizontal section divider header">
        Clearance sale
    </h2>
    {{ render(url('app_admin_custom_product_index')) }}
{% endblock %}


What happens while overriding Repositories?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* The parameter ``sylius.repository.product.class`` contains ``App\Repository\ProductRepository``.
* The repository service ``sylius.repository.product`` is using your new class.
* Under the ``sylius.repository.product`` service you have got all methods from the base repository available plus the one you have added.

.. include:: /customization/plugins.rst.inc

.. _`Query Builder`: http://doctrine-orm.readthedocs.io/projects/doctrine-orm/en/latest/reference/query-builder.html
