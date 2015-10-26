BDD Methodology
===============

.. note::

    This part of documentation is inspired by the official `PHPSpec`_ docs.

Sylius adopted the full-stack BDD methodology for its development processes.

According to `Wikipedia`_:

    "BDD is a software development process based on test-driven development (TDD).
    Behavior-driven development combines the general techniques and principles of TDD with ideas from domain-driven design and object-oriented 
    analysis and design to provide software developers and business analysts with shared tools and a shared process 
    to collaborate on software development, with the aim of delivering software that matters."

Setting up Behat & PHPSpec
--------------------------

To run the entire suite of features and specs, including the ones that depend on external
dependencies, Sylius needs to be able to autoload them. By default, they are
autoloaded from ``vendor/`` under the main root directory (see
``autoload.php.dist``).

To install them all, use `Composer`_:

Step 1: Get `Composer`_

.. code-block:: bash

    $ curl -s http://getcomposer.org/installer | php

Make sure you download ``composer.phar`` in the same folder where
the ``composer.json`` file is located.

Step 2: Install vendors

.. code-block:: bash

    $ php composer.phar install

.. note::

    Note that the script takes some time (several minutes) to finish.

.. note::

    If you don't have ``curl`` installed, you can also just download the ``installer``
    file manually at http://getcomposer.org/installer. Place this file into your
    project and then run:

    .. code-block:: bash

        $ php installer
        $ php composer.phar install

Install Selenium2
~~~~~~~~~~~~~~~~~

Download Selenium server 2.38 `here`_.

.. _here: http://docs.seleniumhq.org/download/

Create a VirtualHost
~~~~~~~~~~~~~~~~~~~~

Add this VirtualHost configuration:

.. code-block:: apache

    <VirtualHost *:80>
        ServerName sylius-test.local

        RewriteEngine On

        DocumentRoot /var/www/sylius/web
        <Directory /var/www/sylius/web>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride None
            Order allow,deny
            allow from all
        </Directory>

        RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME} !-f
        RewriteRule ^(.*) %{DOCUMENT_ROOT}/app_test.php [QSA,L]

        ErrorLog ${APACHE_LOG_DIR}/sylius-test-error.log

        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/sylius-test-access.log combined

    </VirtualHost>

Update your ``/etc/hosts`` file to include the VirtualHost hostname:

.. code-block:: bash

    127.0.0.1   sylius-test.local

Additionally, copy ``behat.yml.dist`` to ``behat.yml``, edit base_url parameter to match your host:

.. code-block:: yaml

    default:
        ...
        extensions:
            Behat\MinkExtension\Extension:
                ...
                base_url: http://sylius-test.local/app_test.php/

Behat
-----

We use `Behat`_ for StoryBDD and you should always write new scenarios when adding a feature, or update existing stories to adapt Sylius to business requirements changes.

Sylius is an open source project, so the **client** is not clearly visible at first look. But they are here - the Sylius users. We have our needs and Behat helps us understand and satisfy these needs.

.. note::

    To be written.

You can launch Selenium by issuing the following command:

.. code-block:: bash

  $ java -jar selenium-server-standalone-2.38.0.jar
  
Configure behat for Selenium:

.. code-block:: yaml

    default:
        ...
        extensions:
            Behat\MinkExtension\Extension:
                default_session: selenium2
                browser_name: firefox
                base_url: http://sylius-test.local/app_test.php
                selenium2:                    
                    capabilities: { "browser": "firefox", "version": "28"}

Run your scenario using the ``behat`` console:

.. code-block:: bash

  $ bin/behat

PHPSpec
-------

PHPSpec is a PHP toolset to drive emergent design by specification.
It is not really a testing tool, but a design instrument, which helps structuring the objects and how they work together.

Sylius approach is to always describe the behavior of the next object you are about to implement.

As an example, we'll write a service, which updates product prices based on an external API.
To initialize a new spec, use the ``desc`` command.

We just need to tell **PHPSpec** we will be working on
the `PriceUpdater` class.

.. code-block:: bash

    $ bin/phpspec desc "Sylius\Bundle\CoreBundle\Pricing\PriceUpdater"
    Specification for PriceUpdater created in spec.

What have we just done? **PHPSpec** has created the spec for us. You can
navigate to the spec folder and see the spec there:

.. code-block:: php

    <?php

    namespace spec\Sylius\Bundle\CoreBundle\Pricing;

    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class PriceUpdaterSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('Sylius\Bundle\CoreBundle\Pricing\PriceUpdater');
        }
    }

The object behavior is made of examples. Examples are encased in public methods,
started with ``it_``.
or ``its_``.

**PHPSpec** searches for such methods in your specification to run.
Why underscores for example names? ``just_because_its_much_easier_to_read``
than ``someLongCamelCasingLikeThat``.

Now, let's write first example which will update the products price:

.. code-block:: php

    <?php

    namespace spec\Sylius\Bundle\CoreBundle\Pricing;

    use Acme\ApiClient;
    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;
    use Sylius\Bundle\CoreBundle\Model\ProductInterface;

    class PriceUpdaterSpec extends ObjectBehavior
    {
        function let(ApiClient $api)
        {
            $this->beConstructedWith($api);
        }

        function it_updates_product_price_through_api($api, ProductInterface $product)
        {
            $product->getSku()->shouldBeCalled()->willReturn('TES-12-A-1090');
            $api->getCurrentProductPrice('TES-12-A-1090')->shouldBeCalled()->willReturn(1545);
            $product->setPrice(1545)->shouldBeCalled();

            $this->updatePrice($product);
        }
    }

The example looks clear and simple, the ``PriceUpdater`` service should obtain the SKU of the product, call the external API and update products price accordingly.

Try running the example by using the following command:

.. code-block:: bash

    $ bin/phpspec run 

    > spec\Sylius\Bundle\CoreBundle\Pricing\PriceUpdater

      ✘ it updates product price through api
          Class PriceUpdater does not exists.

             Do you want me to create it for you? [Y/n]

Once the class is created and you run the command again, PHPSpec will ask if it should create the method as well.
Start implementing the very initial version of the price updater.

.. code-block:: php

    <?php

    namespace Sylius\Bundle\CoreBundle\Pricing;

    use Sylius\Bundle\CoreBundle\Model\ProductInterface;
    use Acme\ApiClient;

    class PriceUpdater
    {
        private $api;

        public function __construct(ApiClient $api)
        {
            $this->api = $api;
        }

        public function updatePrice(ProductInterface $product)
        {
            $price = $this->api->getCurrentProductPrice($product->getSku());
            $product->setPrice($price);
        }
    }

Done! If you run PHPSpec again, you should see the following output:

.. code-block:: bash

    $ bin/phpspec run 
    
    > spec\Sylius\Bundle\CoreBundle\Pricing\PriceUpdater
    
      ✔ it updates product price through api
    
    1 examples (1 passed)
    223ms

This example is greatly simplified, in order to illustrate how we work.
There should be few more examples, which cover errors, API exceptions and other edge-cases.

Few tips & rules to follow when working with PHPSpec & Sylius:

* RED is good, add or fix the code to make it green;
* RED-GREEN-REFACTOR is our rule;
* All specs must pass;
* When writing examples, **describe** the behavior of the object in present tense;
* Omit the ``public`` keyword;
* Use underscores (``_``) in the examples;
* Use type hinting to mock and stub classes;
* If your specification is getting too complex, the design is wrong, try decoupling a bit more;
* If you cannot describe something easily, probably you should not be doing it that way;
* shouldBeCalled or willReturn, never together, except for builders;
* Use constants in assumptions but strings in expected results;

Happy coding!

.. _`Composer`: http://getcomposer.org/
.. _`Wikipedia`: http://en.wikipedia.org/wiki/Behavior-driven_development
