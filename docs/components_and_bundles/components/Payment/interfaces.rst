Interfaces
==========

Model Interfaces
----------------

.. _component_payment_model_payment-interface:

PaymentInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by any custom model representing a payment.
Also it keeps all of the default :ref:`component_payment_payment-states`.

.. note::
   This interface extends the :ref:`component_resource_model_code-aware-interface` and
   :ref:`component_resource_model_timestampable-interface`.

   For more detailed information go to `Sylius API PaymentInterface`_.

.. _Sylius API PaymentInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentInterface.html

.. _component_payment_model_payment-method-interface:

PaymentMethodInterface
~~~~~~~~~~~~~~~~~~~~~~

In order to create a custom payment method class, which could be used by other
models or services from this component, it needs to implement this interface.

.. note::
   This interface extends the :ref:`component_resource_model_timestampable-interface`
   and the :ref:`component_payment_model_payment-method-translation-interface`.

   For more detailed information go to `Sylius API PaymentMethodInterface`_.

.. _Sylius API PaymentMethodInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentMethodInterface.html

.. _component_payment_model_payment-methods-aware-interface:

PaymentMethodsAwareInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any custom
storage used to store representations of the payment method.

.. note::
   For more detailed information go to `Sylius API PaymentMethodsAwareInterface`_.

.. _Sylius API PaymentMethodsAwareInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentMethodsAwareInterface.html

.. _component_payment_model_payment-method-translation-interface:

PaymentMethodTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is needed in creating a custom payment method translation class,
which then could be used by the payment method itself.

.. note::
   For more detailed information go to `Sylius API PaymentMethodTranslationInterface`_.

.. _Sylius API PaymentMethodTranslationInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentMethodTranslationInterface.html

.. _component_payment_model_payment-source-interface:

PaymentSourceInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface needs to be implemented by any custom payment source.

.. note::
   For more detailed information go to `Sylius API PaymentSourceInterface`_.

.. _Sylius API PaymentSourceInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentSourceInterface.html

.. _component_payment_model_payments-subject-interface:

PaymentsSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~

Any container which manages multiple payments should implement this interface.

.. note::
   For more detailed information go to `Sylius API PaymentsSubjectInterface`_.

.. _Sylius API PaymentsSubjectInterface: http://api.sylius.org/Sylius/Component/Payment/Model/PaymentsSubjectInterface.html

Service Interfaces
------------------

.. _component_payment_repository_payment-method-repository-interface:

PaymentMethodRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by your custom repository,
used to handle payment method objects.

.. note::
   For more detailed information go to `Sylius API PaymentMethodRepositoryInterface`_.

.. _Sylius API PaymentMethodRepositoryInterface: http://api.sylius.org/Sylius/Component/Payment/Repository/PaymentMethodRepositoryInterface.html
