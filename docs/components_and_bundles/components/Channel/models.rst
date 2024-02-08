.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_channel_model_channel:

Channel
-------

Sale channel is represented by a **Channel** model. It should have everything
concerning channel's data and as default has the following properties:

+-------------+----------------------------------------+
| Property    | Description                            |
+=============+========================================+
| id          | Unique id of the channel               |
+-------------+----------------------------------------+
| code        | Channel's code                         |
+-------------+----------------------------------------+
| name        | Channel's name                         |
+-------------+----------------------------------------+
| description | Channel's description                  |
+-------------+----------------------------------------+
| url         | Channel's URL                          |
+-------------+----------------------------------------+
| color       | Channel's color                        |
+-------------+----------------------------------------+
| enabled     | Indicates whether channel is available |
+-------------+----------------------------------------+
| createdAt   | Date of creation                       |
+-------------+----------------------------------------+
| updatedAt   | Date of update                         |
+-------------+----------------------------------------+

.. note::
   This model implements :ref:`component_channel_model_channel-interface`.
