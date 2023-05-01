.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_payment_model_payment-interface:

PaymentInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by any custom model representing a payment.
Also it keeps all of the default :ref:`component_payment_payment-states`.

.. note::
   This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and
   `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_payment_model_payment-method-interface:

PaymentMethodInterface
~~~~~~~~~~~~~~~~~~~~~~

In order to create a custom payment method class, which could be used by other
models or services from this component, it needs to implement this interface.

.. note::
   This interface extends the `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_
   and the :ref:`component_payment_model_payment-method-translation-interface`.

.. _component_payment_model_payment-methods-aware-interface:

PaymentMethodsAwareInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any custom
storage used to store representations of the payment method.

.. _component_payment_model_payment-method-translation-interface:

PaymentMethodTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is needed in creating a custom payment method translation class,
which then could be used by the payment method itself.

.. _component_payment_model_payment-source-interface:

PaymentSourceInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface needs to be implemented by any custom payment source.

.. _component_payment_model_payments-subject-interface:

PaymentsSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~

Any container which manages multiple payments should implement this interface.

Service Interfaces
------------------

.. _component_payment_repository_payment-method-repository-interface:

PaymentMethodRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by your custom repository,
used to handle payment method objects.
