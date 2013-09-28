Events
======

All Sylius bundles are using :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>`, which has some built-in events.

Events reference
----------------

+-------------------------------+----------------+
| Event                         | Description    |
+===============================+================+
| sylius.<resource>.pre_create  | Before persist |
+-------------------------------+----------------+
| sylius.<resource>.create      | After persist  |
+-------------------------------+----------------+
| sylius.<resource>.post_create | After flush    |
+-------------------------------+----------------+
| sylius.<resource>.pre_update  | Before persist |
+-------------------------------+----------------+
| sylius.<resource>.update      | After persist  |
+-------------------------------+----------------+
| sylius.<resource>.post_update | After flush    |
+-------------------------------+----------------+
| sylius.<resource>.pre_delete  | Before remove  |
+-------------------------------+----------------+
| sylius.<resource>.delete      | After remove   |
+-------------------------------+----------------+
| sylius.<resource>.post_delete | After flush    |
+-------------------------------+----------------+

CRUD events example
-------------------

Let's take the **Product** model as an example. As you should already know, the product controller is represented by the ``sylius.controller.product`` service.
Several useful events are dispatched during execution of every default action of this controller.
When creating a new resource via the ``createAction`` method, 3 events occur.

First, before the ``persist()`` is called on the product, the ``sylius.product.pre_create`` event is dispatched.

Secondly, just before the database flush is performed, Sylius dispatches the ``sylius.product.create`` event.

Finally, after the data storage is updated, ``sylius.product.post_create`` is triggered.

The same set of events is available for the ``update`` and ``delete`` operations.
All the dispatches are using the ``GenericEvent`` class and return the **Product** object by the ``getSubject`` method.

Registering a listener
----------------------

We'll stay with the **Product** model and create a listener which updates Solr (search engine) document every time a product is updated.

.. code-block:: php

    namespace Acme\ShopBundle\EventListener;

    use Symfony\Component\EventDispatcher\GenericEvent;

    class SolrListener
    {
        // ... Constructor with indexer injection code.

        public function onProductUpdate(GenericEvent $event)
        {
            $this->indexer->updateProductDocument($event->getSubject());
        }
    }

Now you need to register the listener in services configuration.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="acme.listener.solr" class="Acme\ShopBundle\EventListener\SolrListener">
                <tag name="kernel.event_listener" event="sylius.product.post_update" method="onProductUpdate" />
            </service>
        </services>

    </container>

Done! Every time a product is edited and the database updated, this listener will also use the indexer to update Solr index accordingly.
