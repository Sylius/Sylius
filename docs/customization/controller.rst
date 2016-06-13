Customizing Controllers
=======================

All **Sylius** resources are using the `` Sylius\Bundle\ResourceBundle\Controller\ResourceController`` as default. but some of them have been already extended in Bundles.
If you want to override some controller action check which controller you should be extending.

.. note::
    There are two types of controllers we can define in Sylius.
    **Resource Controllers** - are basing only on one Entity, so they return only the resources they have in their name. For instance ProductController should return only Products.
    **Frontend Controllers** - these may use many entities at once, they are useful on more general pages. The best example here is the HomepageController that may return many different objects, such as products, banners, blog posts etc.


Why would you customize a Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To add your custom actions you need to override controllers. You may bee needing to:

* modify your Homepage - add your custom resources, or change template and so on,
* get a bestsellers list and render it,
* get a list of customers that have bought some product,

How to customize a Resource Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Imagine that you would want to render a list of best selling products in a partial template that will be reusable anywhere.
Assuming that you already have a method on the ``ProductRepository`` (you can see such an example :doc:`here </customization/repository>`.
Having this method you may be rendering its result in a new action of the ``ProductController`` using a partial template.

See example below:

1. Create a new Controller class under the ``AppBundle/Controller`` namespace.

Remember that it has to extend a proper base class. How can you check that?

For the ``ProductController`` run:

.. code-block:: bash

    $ php app/console debug:container sylius.controller.product

As a result you will get the ``Sylius\Bundle\CoreBundle\Controller\ProductController`` - this is the class that you need to be extending.

Now you have to create the controller that will have an action for rendering a partial template with your bestselling products.

.. code-block:: php

    <?php

    namespace AppBundle\Controller;

    use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
    use Sylius\Component\Core\Repository\ProductRepositoryInterface;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * @author Name Surname <name.surname@test.com>
     */
    class ProductController extends BaseProductController
    {
        /**
         * @return Response
         */
        public function bestsellersAction()
        {
            /** @var ProductRepositoryInterface $productRepository */
            $productRepository = $this->get('sylius.repository.product');

            $bestsellers = $productRepository->findBySold(4);

            return $this->render('SyliusWebBundle:Frontend/Homepage:_advantages.html.twig', ['bestsellers' => $bestsellers]);
        }
    }

2. In order to use your controller and its actions you need to configure it in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_product:
        resources:
            product:
                classes:
                    controller: AppBundle\Controller\ProductController

How to customize a Frontend Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add the rendering of bestsellers on your Homepage - to do this you should modify the ``mainAction`` of the ``HomepageController``.
You should already have a method on the ``ProductRepository`` (you can see such an example :doc:`here </customization/repository>` that you can call in the controller.

1. Create a new Controller class under the ``AppBundle/Controller/Frontend`` namespace.

Run ``$ php app/console debug:container sylius.controller.frontend.homepage``.
Your class needs to be extending this base class.

.. code-block:: php

    <?php

    namespace AppBundle\Controller\Frontend;

    use Sylius\Component\Core\Repository\ProductRepositoryInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;
    use Sylius\Bundle\WebBundle\Controller\Frontend\HomepageController as BaseHomepageController;
    use Symfony\Component\HttpFoundation\Response;

    /**
     * @author Name Surname <name.surname@test.com>
     */
    class HomepageController extends BaseHomepageController
    {
        /**
         * @return Response
         */
        public function mainAction()
        {
            /** @var RepositoryInterface $customEntityRepository */
            $customEntityRepository = $this->get('app.repository.custom_entity');
            /** @var ProductRepositoryInterface $productRepository */
            $productRepository = $this->get('sylius.repository.product');

            $customEntities = $customEntityRepository->findBy(['criteria' => true]);
            $bestsellers = $productRepository->findBySold(4);

            return $this->render(
                'SyliusWebBundle:Frontend/Homepage:main.html.twig',
                [
                    'customEntities' => $customEntities,
                    'bestsellers' => $bestsellers,
                ]
            );
        }
    }

2. The next thing you have to do is to override the ``sylius.controller.frontend.homepage.class`` parameter in ``AppBundle/Resources/config/services.yml``.

.. code-block:: yaml

    parameters:
        sylius.controller.frontend.homepage.class: AppBundle\Controller\Frontend\HomepageController

From now on your ``mainAction`` of the ``HomepageController`` will be rendering bestsellers, that will be available
in the ``SyliusWebBundle:Frontend/Homepage:main.html.twig`` view under the ``bestsellers`` variable.
