Configuration reference
=======================

.. code-block:: yaml

    sylius_archetype:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          resources:
              # `subject_name` can be any name, for example `product`, `ad`, or `blog_post`
              subject_name:
                  subject:   ~ # Required: The subject class implementing `ArchetypeSubjectInterface`.
                  attribute: Sylius\Attribute\Model\Attribute
                  option:    Sylius\Variation\Model\Option
                  archetype:
                      classes:
                          model:      Sylius\Archetype\Model\Archetype
                          interface:  Sylius\Archetype\Model\ArchetypeInterface
                          controller: Sylius\ResourceBundle\Controller\ResourceController
                          repository: ~ # Required: The repository class for the archetype
                          factory:    Sylius\Resource\Factory\Factory
                          form:
                              default: Sylius\ArchetypeBundle\Form\Type\ArchetypeType
                      validation_groups:
                          default: [ sylius ]
                      translation:
                          classes:
                             model:      Sylius\Archetype\Model\ArchetypeTranslation
                             interface:  Sylius\Archetype\Model\ArchetypeTranslationInterface
                             repository: ~ # Required: The repository class for the archetype translation
                             factory:    Sylius\Resource\Factory\Factory
                             form:
                                 default: Sylius\ArchetypeBundle\Form\Type\ArchetypeTranslationType
                          fields:
                             default: [ name ]
