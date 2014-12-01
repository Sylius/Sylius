Configuration reference
=======================

.. code-block:: yaml

    sylius_attribute:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          classes:
              # `subject_name` can be any name, for example `product`, `ad`, or `blog_post`
              subject_name:
                  subject: ~ # Required: The subject class implementing `AttributeSubjectInterface`.
                  attribute:
                      model:      ~ # Required: The attribute model class implementing `AttributeInterface`.
                      repository: ~ # Required: The repository class for the attribute.
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\AttributeBundle\Form\Type\AttributeType
                  attribute_value:
                      model:      ~ # Required: The attribute value model class implementing `AttributeValueInterface`.
                      repository: ~ # Required: The repository class for the attribute value.
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType
          validation_groups:
              # `subject_name` should be same name as the name key defined for the classes section above.
              subject_name:
                  attribute:       [ sylius ]
                  attribute_value: [ sylius ]
