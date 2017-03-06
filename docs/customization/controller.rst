Customizing Controllers
=======================

All **Sylius** resources use the
`Sylius/Bundle/ResourceBundle/Controller/ResourceController <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ResourceBundle/Controller/ResourceController.php>`_
by default, but some of them have already been extended in Bundles.
If you want to override a controller action, check which controller you should be extending.

.. note::

    There are two types of controllers we can define in Sylius:

    **Resource Controllers** - are based only on one Entity, so they return only the resources they have in their name. For instance a ``ProductController`` should return only products.

    **Standard Controllers** - non-resource; these may use many entities at once, they are useful on more general pages.
    We are defining these controllers only if the actions we want cannot be done through yaml configuration - like sending emails.

Why would you customize a Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To add your custom actions you need to override controllers. You may need to:

* add a generic action that will render a list of recommended products with a product on its show page.
* render a partial template that cannot be done via yaml resource action.

How to customize a Resource Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Imagine that you would want to render a list of best selling products in a partial template that will be reusable anywhere.
Assuming that you already have a method on the ``ProductRepository`` - you can see such an example :doc:`here </customization/repository>`.
Having this method you may be rendering its result in a new action of the ``ProductController`` using a partial template.

See example below:

**1.** Create a new Controller class under the ``AppBundle/Controller`` namespace.

Remember that it has to extend a proper base class. How can you check that?

For the ``ProductController`` run:

.. code-block:: bash

    $ php bin/console debug:container sylius.controller.product

As a result you will get the ``Sylius\Bundle\ResourceBundle\Controller\ResourceController`` - this is the class that you need to extend.

Now you have to create the controller that will have a generic action that is basically the ``showAction`` from the ``ResourceController`` extended by
getting a list of recommended products from your external api.

.. code-block:: php

    <?php

    namespace AppBundle\Controller;

    use FOS\RestBundle\View\View;
    use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Sylius\Component\Resource\ResourceActions;

    class ProductController extends ResourceController
    {
        /**
         * @param Request $request
         *
         * @return Response
         */
        public function showAction(Request $request)
        {
            $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

            $this->isGrantedOr403($configuration, ResourceActions::SHOW);
            $product = $this->findOr404($configuration);

            $recommendationServiceApi = $this->get('app.recommendation_service_api');

            $recommendedProducts = $recommendationServiceApi->getRecommendedProducts($product);

            $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $product);

            $view = View::create($product);

            if ($configuration->isHtmlRequest()) {
                $view
                    ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
                    ->setTemplateVar($this->metadata->getName())
                    ->setData([
                        'configuration' => $configuration,
                        'metadata' => $this->metadata,
                        'resource' => $product,
                        'recommendedProducts' => $recommendedProducts,
                        $this->metadata->getName() => $product,
                    ])
                ;
            }

            return $this->viewHandler->handle($configuration, $view);
        }
    }

**2.** In order to use your controller and its actions you need to configure it in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_product:
        resources:
            product:
                classes:
                    controller: AppBundle\Controller\ProductController

How to customize a Standard Controller:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add some logic to the Homepage.

**1.** Create a new Controller class under the ``AppBundle/Controller/Shop`` namespace.

If you still need the methods of the original HomepageController, then copy its body to the new class.

.. code-block:: php

    <?php

    namespace AppBundle\Controller\Shop;

    use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    final class HomepageController
    {
        /**
         * @var EngineInterface
         */
        private $templatingEngine;

        /**
         * @param EngineInterface $templatingEngine
         */
        public function __construct(EngineInterface $templatingEngine)
        {
            $this->templatingEngine = $templatingEngine;
        }

        /**
         * @param Request $request
         *
         * @return Response
         */
        public function indexAction(Request $request)
        {
            return $this->templatingEngine->renderResponse('@SyliusShop/Homepage/index.html.twig');
        }

        /**
         * @param Request $request
         *
         * @return Response
         */
        public function customAction(Request $request)
        {
            // Put your custom logic here
        }
    }

**2.** The next thing you have to do is to override the ``sylius.controller.shop.homepage`` service definition in the ``app/config/services.yml``.

.. code-block:: yaml

    # app/config/services.yml
    services:
        sylius.controller.shop.homepage:
            class: AppBundle\Controller\Shop\HomepageController
            arguments: ['@templating']

Remember to import the ``app/config/services.yml`` into the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "services.yml" }

.. tip::

    Run ``$ php bin/console debug:container sylius.controller.shop.homepage`` to check if the class has changed to your implementation.

From now on your ``customAction`` of the ``HomepageController`` will be available alongside the ``indexAction`` from the base class.
