Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_resource:
        settings:
            # Enable pagination
            paginate: true
            # If the pagination is disabled, you can specify a limit
            limit: false
            # If the pagination is enabled, you can specify the allowed page size
            allowed_paginate: [10, 20, 30]
            # Default page size
            default_page_size: 10
            # Enable sorting
            sortable: false
            # Default sorting parameters
            sorting: []
            # Enable filtering
            filterable: false
            # Default filtering parameters
            criteria: []
        resources:
            app.user:
                driver: doctrine/orm # Also supported - doctrine/mongodb-odm.
                templates: AppBundle:User
                classes:
                    model: App\Entity\User
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository

Route configuration reference
-----------------------------

.. code-block:: yaml

    route_name:
        defaults:
            _sylius:
                # Name of the form, by default it is built with the prefix of the bundle and the name of the resource
                form: bundlePrefix_resource # string
                # Name of the route where the user will be redirected
                redirect: my_route # string
                # If your route has extra parameters you can use the following syntax:
                    route: my_route # string
                    parameters: [] # array
                # Number of item in the list (should be set to false if you want to disable it)
                limit: 10 # integer or boolean
                # Number of item by page (should be set to false if you want to disable it)
                paginate 10 # integer or boolean
                # Enabling the filter
                filterable: true # boolean
                # Parameter used for filtering resources when filterable is enabled, using a $ to find the parameter in the request
                criteria: $paginate # string or array
                # Enabling the sorting
                sortable: false # boolean
                # Parameter used for sorting resources when sortable is enabled, using a $ to find the parameter in the request
                sorting: $sorting # string or array
                # The method of the repository used to retrieve resources
                method: findActiveProduct # string
                # Arguments gave to the 'method'
                arguments: [] # array
                factory:
                    # The method of the repository used to create the new resource
                    method: createNew # string
                    # Arguments gave to the 'method'
                    arguments: [] # array
                # Key used by the translator for managing flash messages, by default it is built with the prefix of the bundle, the name of the resource and the name of the action (create, update, delete and move)
                flash: sylius.product.create # string
                # Name of the property used to manage the position of the resource
                sortable_position: position # string
                # API request, version used by the serializer
                serialization_version: null
                # API request, groups used by the serializer
                serialization_groups: []

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
