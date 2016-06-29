Configuration reference
=======================

.. code-block:: yaml

    sylius_attribute:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          resources:
              # `subject_name` can be any name, for example `product`, `ad`, or `blog_post`
              subject_name:
                  subject: ~ # Required: The subject class implementing `AttributeSubjectInterface`.
                  attribute:
                      classes:
                          model:      Sylius\Attribute\Model\Attribute
                          interface:  Sylius\Attribute\Model\AttributeInterface
                          repository: Sylius\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository
                          controller: Sylius\ResourceBundle\Controller\ResourceController
                          factory:    Sylius\Resource\Factory\Factory
                          form:
                              default: Sylius\AttributeBundle\Form\Type\AttributeType
                              choice:  Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                      validation_groups:
                          default: [ sylius ]
                      translation:
                          classes:
                              model:      Sylius\Attribute\Model\AttributeTranslation
                              interface:  Sylius\Attribute\Model\AttributeTranslationInterface
                              controller: Sylius\ResourceBundle\Controller\ResourceController
                              repository: ~ # Required: The repository class for the attribute translation.
                              factory:    Sylius\Resource\Factory\Factory
                              form:
                                  default: Sylius\AttributeBundle\Form\Type\AttributeTranslationType
                          validation_groups:
                              default: [ sylius ]
                  attribute_value:
                      classes:
                          model:      ~ # Required: The model of the attribute value
                          interface:  ~ # Required: The interface of the attribute value
                          controller: Sylius\ResourceBundle\Controller\ResourceController
                          repository: ~ # Required: The repository class for the attribute value.
                          factory:    Sylius\Resource\Factory\Factory
                          form:
                              default: Sylius\AttributeBundle\Form\Type\AttributeValueType
                      validation_groups:
                          default: [ sylius ]
