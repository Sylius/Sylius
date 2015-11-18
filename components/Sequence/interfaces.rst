Interfaces
==========

Model Interfaces
----------------

.. _component_sequence_model_sequence-interface:

SequenceInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a **Sequence**.

.. note::

    You will find more information about this interface in `Sylius API SequenceInterface`_.

.. _Sylius API SequenceInterface: http://api.sylius.org/Sylius/Component/Sequence/Model/SequenceInterface.html

.. _component_sequence_model_sequence-subject-interface:

SequenceSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~

To characterize an object with attributes and options from a sequence, the object class needs to implement the ``SequenceSubjectInterface``.

.. note::

    You will find more information about this interface in `Sylius API SequenceSubjectInterface`_.

.. _Sylius API SequenceSubjectInterface: http://api.sylius.org/Sylius/Component/Sequence/Model/SequenceSubjectInterface.html

Service Interfaces
------------------

.. _component_sequence_number_generator-interface:

GeneratorInterface
~~~~~~~~~~~~~~~~~~

This interface gives a possibility to generate and apply next available number for a given subject.

.. note::

    You will find more information about this interface in `Sylius API GeneratorInterface`_.

.. _Sylius API GeneratorInterface: http://api.sylius.org/Sylius/Component/Sequence/Number/GeneratorInterface.html

.. _component_sequence_repository_hash-subject-repository-interface:

HashSubjectRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Repository interface for model which needs number uniqueness check before applying. It provides a method ``isNumberUsed()``.

.. note::

    You will find more information about this interface in `Sylius API HashSubjectRepositoryInterface`_.

.. _Sylius API HashSubjectRepositoryInterface: http://api.sylius.org/Sylius/Component/Sequence/Repository/HashSubjectRepositoryInterface.html
