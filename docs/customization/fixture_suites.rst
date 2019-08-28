Customizing Fixture Suites
==========================

What are fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~

Suites are predefined groups of fixtures that can be run together. They can be for example full shop configurations for manual tests purposes.

Why would you customize fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    * tailoring the default Sylius fixture suite to your needs (removing Orders for example)
    * creating your own fixture suite

How to use suites?
~~~~~~~~~~~~~~~~~~

Complete list of suites can be shown with the ``bin/console sylius:fixtures:list`` command.
In the vanilla Sylius implementation, the ``default`` suite is loaded if ``bin/console sylius:fixtures:load`` command
is executed without any additional argument. If you are creating a new suite you must use this command providing the
name of your suite as an argument: ``bin/console sylius:fixtures:load your_custom_suite``.

How to create custom fixture suites?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request
    <https://github.com/Sylius/Customizations/pull/24>`__.

.. tip::

    If you want to create your fixtures with different locale than ``en_US`` you must change the ``locale`` parameter in ``src/config/services.yaml``.

    .. code-block:: yaml

        parameters:
            locale: pl_PL

**1.** Create the ``src/config/packages/fixtures.yaml`` file and add the following code there:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            poland: # custom suite's name
                listeners:
                    orm_purger: ~ # clean your database before running this suite
                    logger: ~

                fixtures:
                    locale: ~
                    currency:
                        options:
                            currencies: ['PLN'] # add desired currencies as an array

                    geographical: # Countries and zones available in your store
                        options:
                            countries:
                                - "PL"
                            zones:
                                PL:
                                    name: "Poland"
                                    countries:
                                        - "PL"

                    channel:
                        options:
                            custom:
                                pl_web_store:
                                    name: "PL Web Store"
                                    code: "PL_WEB"
                                    locales: # choose the locale for this channel
                                        - "%locale%"
                                    currencies: # choose currencies for this channel
                                        - "PLN"
                                    enabled: true
                                    hostname: "localhost"

                    payment_method:
                        options:
                            custom: # create payment methods and choose channels in which it is available
                                cash_on_delivery:
                                    code: "cash_on_delivery"
                                    name: "Cash on delivery"
                                    channels:
                                        - "PL_WEB"

                    shipping_method: # create shipping methods and choose channels in which it is available
                        options:
                            custom:
                                inpost:
                                    code: "inpost"
                                    name: "InPost"
                                    channels:
                                        - "PL_WEB"

                    shop_user: # add customers
                        name: "shop_user"
                        options:
                            random: 2 # the number of users that are random created
                            custom: # individual users
                                -
                                    email: "shop@example.com"
                                    first_name: "John"
                                    last_name: "Doe"
                                    password: "sylius"
                                -
                                    email: "custom@example.com"
                                    first_name: "Marek"
                                    last_name: "Markowski"
                                    password: "sylius"

                    admin_user: # add administrator accounts
                        name: "admin_user"
                        options:
                            custom:
                                -
                                    email: "sylius@example.com"
                                    username: "sylius"
                                    password: "sylius"
                                    enabled: true
                                    locale_code: "%locale%"
                                    first_name: "Jan"
                                    last_name: "Kowalski"
                                -
                                    email: "api@example.com"
                                    username: "api"
                                    password: "sylius-api"
                                    enabled: true
                                    locale_code: "%locale%"
                                    first_name: "Zbigniew"
                                    last_name: "Nowak"
                                    api: true

                    promotion: # add promotions
                        options:
                            custom:
                                black_friday: # promotion with basic settings
                                    code: "black_friday"
                                    name: "Black Friday"
                                    channels:
                                        - "PL_WEB"
                                crazy_weeks: # promotion with more settings
                                    code: "crazy_weeks"
                                    name: "Crazy Weeks"
                                    usage_limit: 10
                                    priority: 2
                                    starts_at: "-7 day" # takes the date 7 days before the date of running the suite
                                    ends_at: "7 day" # takes the date 7 days after the date of running the suite
                                    channels:
                                        - "PL_WEB"
                                    rules:
                                        -
                                            type: "item_total"
                                            configuration:
                                                PL_WEB:
                                                    amount: 100.00
                                    actions:
                                        -
                                            type: "order_percentage_discount"
                                            configuration:
                                                PL_WEB:
                                                    amount: 10.00

                    # add products
                    mug_product:
                        options:
                            amount: 30

                    sticker_product:
                        options:
                            amount: 20

                    book_product:
                        options:
                            amount: 15

                    tshirt_product:
                        options:
                            amount: 15

                    product_review:
                        options:
                            random: 50

                    similar_product_association:
                        options:
                            amount: 20

                    order:
                        options:
                            amount: 20

                    address:
                        options:
                            random: 10 # the number of addresses that are randomly created
                            prototype:
                                country_code: PL

**2.** Load your custom suite with ``$ bin/console sylius:fixtures:load poland`` command.

Learn more
----------

* :doc:`Book: Fixtures </book/architectures/fixtures>`
* :doc:`FixturesBundle </components_and_bundles/bundles/SyliusFixturesBundle/index>`
