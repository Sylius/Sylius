Forms
=====

The bundle ships with a set of useful form types for all models. You can use the defaults or :doc:`override them </customization/form>` with your own forms.

Product form
------------

The product form type is named ``sylius_product`` and you can create it whenever you need, using the form factory.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Controller/ProductController.php

    namespace Acme\ShopBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;

    class DemoController extends Controller
    {
        public function fooAction(Request $request)
        {
            $form = $this->get('form.factory')->create('sylius_product');
        }
    }

The default product form consists of following fields.

+-----------------+----------+
| Field           | Type     |
+=================+==========+
| name            | text     |
+-----------------+----------+
| description     | textarea |
+-----------------+----------+
| metaDescription | text     |
+-----------------+----------+
| metaKeywords    | text     |
+-----------------+----------+

You can render each of these using the usual Symfony way ``{{ form_row(form.description) }}``.
