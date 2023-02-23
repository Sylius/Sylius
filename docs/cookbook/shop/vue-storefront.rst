How to use Vue Storefront PWA with Sylius?
==========================================

.. note::
    `What are PWAs (Progressive Web Apps)? <https://en.wikipedia.org/wiki/Progressive_web_app>`_

2. What is Vue Storefront?
--------------------------

Vue Storefront is an open-source, platform-agnostic, Progressive Web App (PWA) framework 
for building e-commerce applications. It is built using Vue.js for building user interfaces. 
You can think about it as a USB standard for eCommerce frontends - with Vue Storefront 
you get what is required by a PWA standard in a nice, platform-agnostic environment meaning 
it could be connected to any eCommerce backend, including Sylius.

3. How to use Vue Storefront with Sylius?
-----------------------------------------

In order to use Vue Storefront with Sylius you can use the official integration approved 
by both Sylius and Vue Storefront. The integration was built by `BitBag <https://www.bitbag.io>`_, 
Sylius' leading partner. 
The backend part of the integration can be accessed in the GitHub repository 
at `BitBag's organization <https://github.com/BitBagCommerce/SyliusVueStorefront2Plugin>`_.
This part adds all the necessary GraphQL endpoints to Sylius, making it compatible with the Vue Storefront standards. 
The frontend part could be found under the same organization's package named
`SyliusVueStorefront2Frontend package <https://github.com/BitBagCommerce/SyliusVueStorefront2Frontend>`_. 
If you want to see it in action check out the `VSF Demo <https://vsf2-demo.bitbag.io>`_.

4. Installation - the backend part
----------------------------------

This part refers to installing the API backend, based on the `SyliusVueStorefront2Plugin <https://github.com/BitBagCommerce/SyliusVueStorefront2Plugin>`_. 
Original content could be accessed at `this <https://github.com/BitBagCommerce/SyliusVueStorefront2Plugin/blob/main/doc/installation.md>`_  
repository page.

* Require plugin with composer:

.. code-block:: bash

    composer require ``bitbag/vue-storefront2-plugin``

* Add plugin dependencies to your ``config/bundles.php`` file:

.. code-block:: php

    return [
     ...

        BitBag\SyliusVueStorefront2Plugin\BitBagSyliusVueStorefront2Plugin::class => ['all' => true],
    ];

* Enable API in ``config/services.yaml``:

.. code-block:: yaml

    sylius_api:
        enabled: true

* Add plugin mapping path to your ``config/packages/api_platform.yaml`` file as the last element:

.. code-block:: yaml

    api_platform:
        mapping:
            paths:
                - '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/api_resources'

* Import serialization files in ``config/packages/framework.yaml`` file:

.. code-block:: yaml

    framework:
        serializer:
            mapping:
                paths:
                    - '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/serialization'

* Import plugin config files in ``config/packages/bitbag_sylius_vue_storefront2_plugin.yaml``:

.. code-block:: yaml

    imports:
        - { resource: "@BitBagSyliusVueStorefront2Plugin/Resources/config/services.xml" }

You are free to adjust two parameters in the same file:

.. code-block:: yaml

    bitbag_sylius_vue_storefront2:
        refresh_token_lifespan: 2592000 # default value
        test_endpoint: 'http://127.0.0.1:8080/api/v2/graphql' # default value

* Add some external Doctrine mappings:

.. code-block:: yaml

    doctrine:
        orm:
            mappings:
                VueStorefront2:
                    is_bundle: false
                    type: xml
                    dir: '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/doctrine/model'
                    prefix: 'BitBag\SyliusVueStorefront2Plugin\Model'
                    alias: BitBag\SyliusVueStorefront2Plugin

* Change the Sylius Taxon repository class to add some queries required by GraphQL in ``config/packages/_sylius.yaml``:

.. code-block:: yaml

    sylius_taxonomy:
       resources:
          taxon:
             classes:
                repository: BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepository

* If you're extending Sylius `ProductAttributeValue` entity:

Please use our trait inside: ``BitBag\SyliusVueStorefront2Plugin\Model\ProductAttributeValueTrait``. 
Otherwise, please create an entity, which uses the trait and setup the Sylius resource in ``config/packages/_sylius.yaml``. 

Read more on how to customize models in a `different part of Sylius docs <https://sylius-older.readthedocs.io/en/latest/customization/model.html>`_ 
if you are not familiar with the process yet.

.. code-block:: yaml

    sylius_attribute:
        driver: doctrine/orm
        resources:
            product:
                subject: Sylius\Component\Core\Model\Product
                attribute_value:
                    classes:
                        model: App\Entity\ProductAttributeValue

* Add a new column to the ProductAttributeValue entity in your Doctrine configuration file: 

We are using XML for Doctrine mappings but you are free to rewrite it to Annotations or YAML.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping
        xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
    >
        <entity name="App\Entity\ProductAttributeValue" table="sylius_product_attribute_value">
            <indexes>
                <index name="locale_code" columns="locale_code" />
            </indexes>
        </entity>
    </doctrine-mapping>

* Import routing in ``config/routes.yaml``:

.. code-block:: yaml

    bitbag_sylius_vue_storefront2_plugin:
        resource: "@BitBagSyliusVueStorefront2Plugin/Resources/config/routing.yml"

The process seems a bit complex but is required to set up everything properly. 
If something went wrong in your instance at this stage make sure you followed this doc properly step by step.
and check this sample configuration in the plugin environment `plugin environment <https://github.com/BitBagCommerce/SyliusVueStorefront2Plugin/tree/main/tests/Application>`_.

4. Installation - the frontend part
-----------------------------------

* Clone the frontend repository:

.. code-block:: bash

    git clone git@github.com:BitBagCommerce/SyliusVueStorefront2Frontend.git && cd SyliusVueStorefront2Frontend

* Copy the ``packages/theme/.env.dist`` file to ``packages/theme/.env`` and configure your environment in the file:

* Install dependencies:

.. code-block:: bash

    yarn install

* Run the application in dev mode:

.. code-block:: bash

    yarn dev

For production mode use ``yarn start`` command instead.

That is it. You are now able to start your journey with Vue Storefront in your Sylius instance. 
For more details follow `official Vue Storefront documentation <https://docs.vuestorefront.io/v2/>`_.
