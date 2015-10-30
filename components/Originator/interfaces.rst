Interfaces
==========

Model Interface
---------------

.. _component_originator_model_origin-aware-interface:

OriginAwareInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any model which
you want to characterize with an origin.

.. note::
   For more detailed information go to `Sylius API OriginAwareInterface`_.

.. _Sylius API OriginAwareInterface: http://api.sylius.org/Sylius/Component/Originator/Model/OriginAwareInterface.html

Service Interfaces
------------------

.. _component_originator_originator_originator-interface:

OriginatorInterface
~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be capable of getting any
object via an implementation of :ref:`component_originator_model_origin-aware-interface`
and setting an origin object.

.. note::
   For more detailed information go to `Sylius API OriginatorInterface`_.

.. _Sylius API OriginatorInterface: http://api.sylius.org/Sylius/Component/Originator/Originator/OriginatorInterface.html
