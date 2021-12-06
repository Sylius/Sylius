How to add an additional endpoint?
----------------------------------

Let's assume that you want to add a new endpoint to the ``Order`` resource that will be dispatching a command.
If you want to customize any API resource, you need to copy the entire configuration of this resource from
``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/`` to ``%kernel.project_dir%/config/api_platform``.
Add the following configuration in the config copied to ``config/api_platform/Order.xml``:

.. code-block:: xml

    <collectionOperations>
        <collectionOperation name="custom_operation">
            <attribute name="method">POST</attribute>
            <attribute name="path">/shop/orders/custom-operation</attribute>
            <attribute name="messenger">input</attribute>
            <attribute name="input">App\Command\CustomCommand</attribute>
        </collectionOperation>
    </collectionOperations>

And that's all, now you have a new endpoint with your custom logic.

.. tip::

    Read more about API Platform endpoint configuration `here <https://api-platform.com/docs/core/operations/>`_

How to remove an endpoint?
--------------------------

Let's assume that your shop is offering only digital products. Therefore, while checking out,
your customers do not need to choose a shipping method for their orders.

Thus you will need to modify the configuration file of the ``Order`` resource and remove the shipping method choosing endpoint from it.
To remove the endpoint you only need to delete the unnecessary configuration from your ``config/api_platform/Order.xml`` which is a copied configuration file, that overwrites the one from Sylius.

.. code-block:: xml

    <!-- delete this configuration -->
    <itemOperation name="shop_select_shipping_method">
        <!-- ... -->
    </itemOperation>
