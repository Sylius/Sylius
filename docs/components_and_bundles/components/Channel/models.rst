Models
======

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

   For more detailed information go to `Sylius API Channel`_.

.. _Sylius API Channel: http://api.sylius.org/Sylius/Component/Channel/Model/Channel.html
