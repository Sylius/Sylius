Overriding Controllers
======================

All Sylius bundles are using :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>` as a foundation for database storage.

Extending base Controller
-------------------------

If you want to modify the controller or add your custom actions, you can do so by defining a new controller class.
By extending resource controller, you also get access to several handy methods. Let's add to our custom controller a new method to recommend a product:

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

You also need to configure your controller class in ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                controller: Acme\ShopBundle\Controller\ProductController

That's it! Now ``sylius.controller.product:recommendAction`` is available. You can use it by defining a new route.

.. code-block:: yaml

    # app/config/routing.yml

    acme_shop_product_recommend:
        path: /products/{id}/recommend
        defaults:
            _controller: sylius.controller.product:recommendAction

What has happened?

* Parameter ``sylius.controller.product.class`` contains ``Acme\\Bundle\\ShopBundle\\Controller\\ProductController``.
* Controller service ``sylius.controller.product`` is using your new controller class.
