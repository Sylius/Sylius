Configuration reference
=======================

.. code-block:: yaml

    sylius_attribute:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          resources:
              contact_request:
                  classes:
                      model:      Sylius\Contact\Model\Request
                      interface:  Sylius\Contact\Model\RequestInterface
                      controller: Sylius\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Resource\Factory\Factory
                      form:
                          default: Sylius\ContactBundle\Form\Type\RequestType
                  validation_groups:
                      default: [ sylius ]
              contact_topic:
                  classes:
                      model:      Sylius\Contact\Model\Topic
                      interface:  Sylius\Contact\Model\TopicInterface
                      controller: Sylius\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Resource\Factory\Factory
                      form:
                          default: Sylius\ContactBundle\Form\Type\TopicType
                  validation_groups:
                      default: [ sylius ]
                  translation:
                      classes:
                          model:      Sylius\Contact\Model\TopicTranslation
                          interface:  Sylius\Contact\Model\TopicTranslationInterface
                          controller: Sylius\ResourceBundle\Controller\ResourceController
                          repository: ~
                          factory:    Sylius\Resource\Factory\Factory
                          form:
                              default: Sylius\ContactBundle\Form\Type\TopicTranslationType
                      validation_groups:
                          default: [ sylius ]
