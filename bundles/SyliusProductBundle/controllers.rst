Using the Controllers
=====================

When using the bundle, you have access to a controller for each model.
You can use them to manipulate and manage your product catalog.

Controller services
-------------------

All controllers in bundle are powered by :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>`. Please read the documentation to have a complete understanding what you can do.

Overriding controllers
----------------------

If you want to modify the controller or add your custom actions, you can do so by defining a new controller class.
By extending resource controller, you also get access to several handy methods.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Controller/ProductController.php

    namespace Acme\ShopBundle\Controller;

    use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
    use Symfony\Component\HttpFoundation\Request;

    class ProductController extends ResourceController
    {
        public function recommendAction(Request $request, $id)
        {
            $product = $this->findOr404(array('id' => $id)); // Find product with given id or return 404!
            $product->incrementRecommendations(); // Add +1!

            $this->persistAndFlush($product); // Save product.

            return $this->redirect($this->generateUrl('acme_shop_homepage'));
        }
    }

Now you just need to configure your class to be used for Product controller service.

.. code-block:: yaml

    # app/config/config.yml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                model: Acme\ShopBundle\Entity\Product
                controller: Acme\ShopBundle\Controller\ProductController

That's it! Now ``sylius.controller.product:recommendAction`` is available.
You can use it by defining a new route.

.. code-block:: yaml

    # app/config/routing.yml

    acme_shop_product_recommend:
        pattern: /products/{id}/recommend
        defaults:
            _controller: sylius.controller.product:recommendAction
