Customizing Controllers
=======================

All **Sylius** resources are using the
`Sylius/Bundle/ResourceBundle/Controller/ResourceController <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ResourceBundle/Controller/ResourceController.php>`_
as default, but some of them have been already extended in Bundles.
If you want to override some controller action, check which controller you should be extending.

.. note::
    There are two types of controllers we can define in Sylius.
    **Resource Controllers** - are basing only on one Entity, so they return only the resources they have in their name. For instance a ``ProductController`` should return only products.
    **Standard Controllers** - non-resource; these may use many entities at once, they are useful on more general pages. We are extending these controllers only if the actions we want cannot be done through yaml configuration - like sending emails.

Why would you customize a Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To add your custom actions you need to override controllers. You may bee needing to:

* add a generic action that will render a list of recommended products with a product on its show page,
* render a partial template that cannot be done via yaml resource action.

How to customize a Resource Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Imagine that you would want to render a list of best selling products in a partial template that will be reusable anywhere.
Assuming that you already have a method on the ``ProductRepository`` - you can see such an example :doc:`here </customization/repository>`.
Having this method you may be rendering its result in a new action of the ``ProductController`` using a partial template.

See example below:

1. Create a new Controller class under the ``AppBundle/Controller`` namespace.

Remember that it has to extend a proper base class. How can you check that?

For the ``ProductController`` run:

.. code-block:: bash

    $ php app/console debug:container sylius.controller.product

As a result you will get the ``Sylius\Bundle\CoreBundle\Controller\ProductController`` - this is the class that you need to be extending.

Now you have to create the controller that will have a generic action that is basically the ``showAction`` from the ``ResourceController`` extended by
getting a list of recommended product from your external api.

.. code-block:: php

    <?php

    namespace AppBundle\Controller;

    use FOS\RestBundle\View\View;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
    use Sylius\Component\Resource\ResourceActions;

    class ProductController extends BaseProductController
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

2. In order to use your controller and its actions you need to configure it in the ``app/config/config.yml``.

.. code-block:: yaml

    sylius_product:
        resources:
            product:
                classes:
                    controller: AppBundle\Controller\ProductController

How to customize a Standard Controller?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to send some kind of emails (which are not resources) after something has been purchased in your shop - to do this you should modify an ``afterPurchaseAction`` on the ``OrderController``.

1. Create a new Controller class under the ``AppBundle/Controller/Frontend`` namespace.

Run ``$ php app/console debug:container sylius.controller.frontend.order``.

Your class needs to be extending this base class.

.. code-block:: php

    <?php

    namespace AppBundle\Controller\Frontend;

    use Sylius\Bundle\WebBundle\Controller\Frontend\Account\OrderController as BaseOrderController;
    use Sylius\Bundle\PayumBundle\Request\GetStatus;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class OrderController extends BaseOrderController
    {
        /**
         * @param Request $request
         *
         * @return Response
         */
        public function afterPurchaseAction(Request $request)
        {
            $token = $this->getHttpRequestVerifier()->verify($request);
            $this->getHttpRequestVerifier()->invalidate($token);

            $status = new GetStatus($token);
            $this->getPayum()->getGateway($token->getGatewayName())->execute($status);
            $payment = $status->getFirstModel();
            $order = $payment->getOrder();
            $this->checkAccessToOrder($order);

            $this->getOrderManager()->flush();

            $emailManager = $this->get('sylius.email_manager.order');
            $emailManager->sendConfirmationEmail($order);

            return $this->redirectToRoute('sylius_checkout_thank_you');
        }
    }

2. The next thing you have to do is to override the ``sylius.controller.frontend.order.class`` parameter in ``AppBundle/Resources/config/services.yml``.

.. code-block:: yaml

    parameters:
        sylius.controller.frontend.order.class: AppBundle\Controller\Frontend\OrderController

From now on your ``afterPurchaseAction`` of the ``OrderController`` will also send emails in addition to its default behaviour.
