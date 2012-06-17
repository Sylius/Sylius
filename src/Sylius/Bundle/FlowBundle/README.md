SyliusFlowBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusFlowBundle.png)](http://travis-ci.org/Sylius/SyliusFlowBundle)
================

Multiple action processes with reusable steps for any Symfony2 application.
Suitable for building checkouts or installations.

This bundle is a **prototype**, it works only with latest Symfony.

``` php
<?php

namespace Acme\Bundle\CheckoutBundle\Process\Scenario;

use Acme\Bundle\CheckoutBundle\Process\Step;
use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;

/**
 * My super checkout.
 *
 * @author Potato Potato <potato@potato.foo>
 */
class CheckoutScenario implements ProcessScenarioInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ProcessBuilderInterface $builder)
    {
        $builder
            ->add('security', new Step\SecurityStep())
            ->add('delivery', new Step\DeliveryStep())
            ->add('billing', 'acme_checkout_step_billing')
            ->add('finalize', new Step\FinalizeStep())
        ;
    }
}
```

``` php
<?php

namespace Acme\Bundle\CheckoutBundle\Process\Step;

use Acme\Bundle\CheckoutBundle\Entity\Address;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;

/**
 * Delivery step.
 * Allows user to select delivery method for order.
 *
 * @author Potato Potato <potato@potato.foo>
 */
class DeliveryStep extends ContainerAwareStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        // All data is stored in a separate session bag with parameter namespace support.
        $address = $context->getStorage()->get('delivery.address');
        $form = $this->createAddressForm($address);

        return $this->container->get('templating')->renderResponse('AcmeCheckoutBundle:Step:delivery.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $context->getRequest();
        $form = $this->createAddressForm();

        if ($request->isMethod('POST') && $form->bindRequest($request)->isValid()) {
            $context->getStorage()->set('delivery.address', $form->getData());
            $context->complete(); // Complete step, user will be redirected to next step.

            return;
        }

        // Form was not valid so we display the form again.
        return $this->container->get('templating')->renderResponse('AcmeCheckoutBundle:Step:delivery.html.twig', array(
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    /**
     * Create address form.
     *
     * @param Address $address
     *
     * @return FormInterface
     */
    private function createAddressForm(Address $address = null)
    {
        return $this->container->get('form.factory')->create('acme_checkout_address', $address);
    }
}
```

Sylius
------

**Sylius** is simple but **end-user and developer friendly** webshop engine built on top of Symfony2.

Please visit [Sylius.org](http://sylius.org) for more details.

Testing and build status
------------------------

This bundle uses [travis-ci.org](http://travis-ci.org/Sylius/SyliusFlowBundle) for CI.
[![Build status...](https://secure.travis-ci.org/Sylius/SyliusFlowBundle.png)](http://travis-ci.org/Sylius/SyliusFlowBundle)

Before running tests, load the dependencies using [Composer](http://packagist.org).

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install --dev
```

Now you can run the tests by simply using this command.

``` bash
$ phpunit
```

Code examples
-------------

If you want to see working implementation, try out the [Sylius sandbox application](http://github.com/Sylius/Sylius-Sandbox).

Documentation
-------------

Documentation is available on [readthedocs.org](http://sylius.readthedocs.org/en/latest/bundles/SyliusFlowBundle.html).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://sylius.readthedocs.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

If you are using this bundle and have any questions, feel free to ask on users mailing list.
[Mail](mailto:sylius@googlegroups.com) or [view it](http://groups.google.com/group/sylius).

### Developers

If you want to contribute, and develop this bundle, use the developers mailing list.
[Mail](mailto:sylius-dev@googlegroups.com) or [view it](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/_Sylius)
or [follow me](http://twitter.com/pjedrzejewski).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusFlowBundle/issues).
If you have found bug, please create an issue.

Versioning
----------

Releases will be numbered with the format `major.minor.patch`.

And constructed with the following guidelines.

* Breaking backwards compatibility bumps the major.
* New additions without breaking backwards compatibility bumps the minor.
* Bug fixes and misc changes bump the patch.

For more information on SemVer, please visit [semver.org website](http://semver.org/).

This versioning method is same for all **Sylius** bundles and applications.

License
-------

License can be found [here](https://github.com/Sylius/SyliusFlowBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusFlowBundle/contributors).
