How to add images to an entity?
===============================

Extending entities with an `images` field is quite a popular usecase.
In this cookbook we will present how to **add image to the Shipping Method entity**.

Instructions:
-------------

1. Extend the ShippingMethodInterface with the ImageAwareInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

2. Implement the new ShippingMethodInterface in your own ShippingMethod class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

3. Modify the ShippingMethod's mapping file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

4. Register your extended ShippingMethod as a resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

5. Create the ShippingMethodImage class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

6. Add the mapping file for the ShippingMethodImage
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

7. Register the ShippingMethodImage as a resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

6. Create the ShippingMethodImageType class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

7. Register the ShippingMethodImageType as a service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

8. Register the ShippingMethodImageType as a resource form
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

9. Add the ShippingMethodImageType to the resource form configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

10. Extend the ShippingMethodType with the images field
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^



11. Override the definition of the ImageUploader service
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^



12. Render the images field in the form view
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^



Learn more
----------

* :doc:`GridBundle documentation </bundles/SyliusGridBundle/index>`
* :doc:`ResourceBundle documentation </bundles/SyliusResourceBundle/index>`
* :doc:`Customization Guide </customization/index>`
