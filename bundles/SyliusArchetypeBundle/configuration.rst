Configuration reference
=======================

.. code-block:: yaml

    sylius_archetype:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          classes:
              # `subject_name` can be any name, for example `product`, `ad`, or `blog_post`
              subject_name:
                  subject:   ~ # Required: The subject class implementing `ArchetypeSubjectInterface`.
                  attribute: Sylius\Component\Attribute\Model\Attribute
                  option:    Sylius\Component\Variation\Model\Option
                  archetype:
                      model:      Sylius\Component\Archetype\Model\Archetype # The archetype model class implementing `ArchetypeInterface`.
                      repository: ~ # Required: The repository class for the archetype.
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      form:       Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType
          validation_groups:
              # `subject_name` should be same name as the name key defined for the classes section above.
              subject_name:
                  attribute:       [ sylius ]
                  attribute_value: [ sylius ]
