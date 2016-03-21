Configuration reference
=======================

.. code-block:: yaml

    sylius_archetype:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          resources:
              # `subject_name` can be any name, for example `product`, `ad`, or `blog_post`
              subject_name:
                  subject:   ~ # Required: The subject class implementing `ArchetypeSubjectInterface`.
                  attribute: Sylius\Component\Attribute\Model\Attribute
                  option:    Sylius\Component\Variation\Model\Option
                  archetype:
                      classes:
                          model:      Sylius\Component\Archetype\Model\Archetype
                          interface:  Sylius\Component\Archetype\Model\ArchetypeInterface
                          controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                          repository: ~ # Required: The repository class for the archetype
                          factory:    Sylius\Component\Resource\Factory\Factory
                          form:
                              default: Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType
                      validation_groups:
                          default: [ sylius ]
                      translation:
                          classes:
                             model:      Sylius\Component\Archetype\Model\ArchetypeTranslation
                             interface:  Sylius\Component\Archetype\Model\ArchetypeTranslationInterface
                             repository: ~ # Required: The repository class for the archetype translation
                             factory:    Sylius\Component\Resource\Factory\Factory
                             form:
                                 default: Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeTranslationType
                          fields:
                             default: [ name ]
