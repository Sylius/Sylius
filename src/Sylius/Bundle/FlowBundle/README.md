SyliusFlowBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusFlowBundle.png?branch=master)](http://travis-ci.org/Sylius/SyliusFlowBundle)
================

Multiple action processes with reusable steps for [**Symfony2**](http://symfony.com) applications.
Suitable for building checkouts or installations.

> Note: This bundle is a **prototype**, it works only with latest Symfony.

Sylius
------

**Sylius** - Modern ecommerce for Symfony2. Visit [Sylius.org](http://sylius.org).

Usage examples
--------------

```php
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

```php
<?php

namespace Acme\Bundle\CheckoutBundle\Process\Step;

use Acme\Bundle\CheckoutBundle\Entity\Address;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\AbstractContainerAwareStep;

/**
 * Delivery step.
 * Allows user to select delivery method for order.
 *
 * @author Potato Potato <potato@potato.foo>
 */
class DeliveryStep extends AbstractContainerAwareStep
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

        if ($form->handleRequest($request)->isValid()) {
            $context->getStorage()->set('delivery.address', $form->getData());

            return $this->complete();
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

[phpunit](http://phpunit.de) tests
----------------------------------

``` bash
$ composer install
$ phpunit
```

Documentation
-------------

Documentation is available on [**docs.sylius.org**](http://docs.sylius.org/en/latest/bundles/SyliusFlowBundle/index.html).

Code examples
-------------

If you want to see working implementation, try out the [Sylius sandbox application](http://github.com/Sylius/Sylius-Sandbox).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

Questions? Feel free to ask on [users mailing list](http://groups.google.com/group/sylius).

### Developers

To contribute and develop this bundle, use the [developers mailing list](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/Sylius).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
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

MIT License
-----------

License can be found [here](https://github.com/Sylius/SyliusFlowBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusFlowBundle/contributors).
