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
                          model:      Sylius\Component\Attribute\Model\Attribute
                          interface:  Sylius\Component\Attribute\Model\AttributeInterface
                          repository: Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository
                          controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                          factory:    Sylius\Component\Resource\Factory\Factory
                          form: Sylius\Bundle\AttributeBundle\Form\Type\AttributeType
                      translation:
                          classes:
                              model:      Sylius\Component\Attribute\Model\AttributeTranslation
                              interface:  Sylius\Component\Attribute\Model\AttributeTranslationInterface
                              controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                              repository: ~ # Required: The repository class for the attribute translation.
                              factory:    Sylius\Component\Resource\Factory\Factory
                              form: Sylius\Bundle\AttributeBundle\Form\Type\AttributeTranslationType
                  attribute_value:
                      classes:
                          model:      ~ # Required: The model of the attribute value
                          interface:  ~ # Required: The interface of the attribute value
                          controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                          repository: ~ # Required: The repository class for the attribute value.
                          factory:    Sylius\Component\Resource\Factory\Factory
                          form: Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType
