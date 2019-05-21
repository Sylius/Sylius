Customizing Fixture Suites
==========================

What are suites?
~~~~~~~~~~~~~~~~
Suites are pockets of fixtures that can be run together.

Why would you customize a suites?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you need to create a configuration file where you define all necessarily configuration to your store.

How to use suites?
~~~~~~~~~~~~~~~~~~
Complete list of suites is can be shown with the ``$ bin/console sylius:fixtures:list`` command.
In the vanilla Sylius implementation, the ``default`` suite is loaded if ``$ bin/console sylius:fixtures:load`` command is executed without any additional argument.
If you are creating a new suite you must use the previous command with the name of your suite: ``$ bin/console sylius:fixtures:load your_custom_suite``.

How to create suites?
~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of this example on `this GitHub Pull Request
    <https://github.com/Sylius/Customizations/pull/24>`__.

.. warning::

    If you want create your fixtures with different locale than ``en_US`` you must change the ``locale`` parameter in ``src/config/services.yaml``.

    .. code-block:: yaml

        parameters:
            locale: pl_PL

**1.** Create a ``src/config/packages/fixtures.yaml`` file and add below code it there:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            poland: # custom suite's name
                listeners:
                    orm_purger: ~ # clean your database after last changes
                    logger: ~

                fixtures:
                    locale: ~ # add locale in your shop
                    currency:
                        options:
                            currencies: ['PLN'] # add your currencies in table

                    geographical: # you individual geographical settings
                        options:
                            countries: # your country
                                - "PL"
                            zones: # add your zones
                                PL:
                                    name: "Poland"
                                    countries:
                                        - "PL"

                    channel:
                        options:
                            custom:
                                pl_web_store: # define your channel in your store
                                    name: "PL Web Store"
                                    code: "PL_WEB"
                                    locales:
                                        - "%locale%"
                                    currencies: # add currencies in your store
                                        - "PLN"
                                    enabled: true
                                    hostname: "localhost"

                    payment_method:
                        options:
                            custom: # create your custom payment_methods and add channels in it
                                cash_on_delivery:
                                    code: "cash_on_delivery"
                                    name: "Cash on delivery"
                                    channels:
                                        - "PL_WEB"
                                bank_transfer:
                                    code: "bank_transfer"
                                    name: "Bank transfer"
                                    channels:
                                        - "PL_WEB"
                                    enabled: true

                    shipping_method: # create your custom shipping_methods and add channels in it
                        options:
                            custom:
                                ups:
                                    code: "ups"
                                    name: "UPS"
                                    enabled: true
                                    channels:
                                        - "PL_WEB"
                                dhl_express:
                                    channels:
                                        - "PL_WEB"
                                inpost:
                                    code: "inpost"
                                    name: "InPost"
                                    channels:
                                        - "PL_WEB"

                    customer_group: # add customer groups
                        options:
                            custom:
                                retail:
                                    code: "retail"
                                    name: "Retail"
                                wholesale:
                                    code: "wholesale"
                                    name: "Wholesale"

                    shop_user:
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

                    admin_user: # add administrators accounts
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

                    tax_category: # add category taxes
                        options:
                            custom:
                                clothing:
                                    code: "clothing"
                                    name: "Clothing"
                                books:
                                    code: "books"
                                    name: "Books"
                                other:
                                    code: "other"
                                    name: "Other"

                    tax_rate: # add definition of tax rate and necessarily configuration
                        options:
                            custom:
                                clothing_tax:
                                    code: "clothing_sales_tax_10"
                                    name: "Clothing Sales Tax 10%"
                                    zone: "PL"
                                    category: "clothing"
                                    amount: 0.1
                                books_tax:
                                    code: "books_sales_tax_5"
                                    name: "Books Sales Tax 5%"
                                    zone: "PL"
                                    category: "books"
                                    amount: 0.05
                                default_sales_tax:
                                    code: "sales_tax_20"
                                    name: "Sales Tax 20%"
                                    zone: "PL"
                                    category: "other"
                                    amount: 0.2

                    promotion: # add your promotion
                        options:
                            custom:
                                black_friday: # promotion with primary settings
                                    code: "black_friday"
                                    name: "Black Friday"
                                    channels:
                                        - "PL_WEB"
                                new_year: # promotion with more settings
                                    code: "new_year"
                                    name: "New Year"
                                    usage_limit: 10
                                    priority: 2
                                    starts_at: "-7 day"
                                    ends_at: "7 day"
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
                                            type: "order_fixed_discount"
                                            configuration:
                                                PL_WEB:
                                                    amount: 10.00

                    # add your products:
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
                            random: 10 # the number of addresses that are random created
                            prototype:
                                country_code: PL

**2.** Load your custom suite with ``$ bin/console sylius:fixtures:load poland`` command.

Learn more
##########

* :doc:`FixtureBundle </components_and_bundles/bundles/SyliusFixturesBundle/index>`
