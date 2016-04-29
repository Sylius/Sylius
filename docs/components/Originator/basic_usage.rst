Basic Usage
===========

.. _component_originator_originator_originator:

Originator
----------

This service searches a repository for the origin object of given object implementing
the :ref:`component_originator_model_origin-aware-interface` and returns it.

Let's say we have class:

.. code-block:: php

   <?php

   namespace Example\Model;

   class Ancestor
   {
       /**
        * @var int
        */
       $identifier;

       /**
        * @param int $id
        */
       public function __construct($id = null)
       {
           $this->identifier = $id;
       }

       /**
        * @return int
        */
       public function getIdentifier()
       {
           return $this->identifier;
       }
   }

.. note::
   Any class used as an **origin** needs to have a **getter** for its **identifier**.

and:

.. code-block:: php

   <?php

   namespace Example\Model;

   use Sylius\Component\Originator\Model\OriginAwareInterface;

   class OriginAware implements OriginAwareInterface
   {
       /**
        * @var int
        */
       $originId;

       /**
        * @var string
        */
       $originType;

       // all of the interface's methods
   }

Now, to create a new **Originator** you will need
a service which implements the Doctrine's ManagerRegistry and
the requested object's identifier property name (the default setting is 'id'):

.. code-block:: php

   <?php

   use Sylius\Component\Originator\Originator\Originator;
   use //the ManagerRegistry service, here: $registry

   $originator = new Originator($registry); //using the default field name

   //or

   $originator = new Originator($registry, 'identifier');

Setting the origin of an object is really simple:

.. code-block:: php

   <?php

   use Sylius\Component\Originator\Originator\Originator;
   use Example\Model\Ancestor;
   use Example\Model\OriginAware;
   use //the ManagerRegistry service, here: $registry

   $ancestor = new Ancestor(2);
   $emptyAncestor = new Ancestor();

   $aware = new OriginAware();

   $originator = new Originator($registry, 'identifier');

   $originator->setOrigin($aware, $emptyAncestor); // will throw an exception as the origin's
                                                // id field needs to be set

   $originator->setOrigin($aware, $ancestor); // this however is successful

   $aware->getOriginId(); // returns 2
   $aware->getOriginType(); // returns 'Example\Model\Ancestor'

Now, with origin set in ``$aware`` object and if we have our ``$ancestor`` in a repository we can:

.. code-block:: php

   $originator->getOrigin($aware); // gets the origin object from it's repository
                                   // and returns it, so in this case returns the $ancestor

.. note::
   This service implements the :ref:`component_originator_originator_originator-interface`.
