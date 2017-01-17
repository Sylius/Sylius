Forms
=====

The bundle ships with a set of useful form types for all models. You can use the defaults or :doc:`override them </customization/form>` with your own types.

Address form
------------

The address form type is named ``sylius_address`` and you can create it whenever you need, using the form factory.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Controller/AddressController.php

    namespace Acme\ShopBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;

    class DemoController extends Controller
    {
        public function fooAction(Request $request)
        {
            $form = $this->get('form.factory')->create('sylius_address');
        }
    }

You can also embed it into another form.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Form/Type/OrderType.php

    namespace Acme\ShopBundle\Form\Type;

    use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;
    use Symfony\Component\Form\FormBuilderInterface;

    class OrderType extends BaseOrderType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);

            $builder
                ->add('billingAddress', 'sylius_address')
                ->add('shippingAddress', 'sylius_address')
            ;
        }
    }
