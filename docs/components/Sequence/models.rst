Models
======

.. _component_sequence_model_sequence:

Sequence
--------

**Sequence** is a generated set of numbers and/or letters stored on a model. They may be useful for example as order or customer identifiers.

+------------+------------------------------+
| Property   | Description                  |
+============+==============================+
| id         | Unique id of the sequence    |
+------------+------------------------------+
| index      | The sequence's index         |
+------------+------------------------------+
| type       | The sequence's type          |
+------------+------------------------------+

.. note::
   This model implements the :ref:`component_sequence_model_sequence-interface`.

   For more detailed information go to `Sylius API Sequence`_.

.. _Sylius API Sequence: http://api.sylius.org/Sylius/Component/Sequence/Model/Sequence.html
