Forms
=====

The bundle ships with a set of useful form types for all models. You can use the defaults or :doc:`override them </bundles/general/overriding_forms>` with your own forms.

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
| availableOn     | datetime |
+-----------------+----------+
| metaDescription | text     |
+-----------------+----------+
| metaKeywords    | text     |
+-----------------+----------+

You can render each of these using the usual Symfony way ``{{ form_row(form.description) }}``.

Property form
-------------

Default form for the Property model has name ``sylius_property`` and contains several basic fields.

+--------------+--------+
| Field        | Type   |
+==============+========+
| name         | text   |
+--------------+--------+
| presentation | text   |
+--------------+--------+
| type         | choice |
+--------------+--------+

Prototype form
--------------

The default form for the Prototype model has name ``sylius_prototype`` and is built from the following fields.

+------------+------------------------+
| Field      | Type                   |
+============+========================+
| name       | text                   |
+------------+------------------------+
| properties | sylius_property_choice |
+------------+------------------------+


Miscellaneous fields
--------------------

There are a few more form types, which can become useful when integrating the bundle into your app.

``sylius_product_property`` is a form which is used to set the product properties (and their values). It has 2 fields, the property choice field and a value input.

``sylius_property_choice`` is a ready-to-use select field, with a list of all Properties from database.

``sylius_product_to_identifier`` can be used to render a text field, which will transform the value into a product.

**If you need to customize existing fields or add your own, please read the** :doc:`overriding forms chapter </bundles/general/overriding_forms>`.
