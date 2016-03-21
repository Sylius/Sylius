Configuration reference
=======================

.. code-block:: yaml

    sylius_attribute:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          resources:
              contact_request:
                  classes:
                      model:      Sylius\Component\Contact\Model\Request
                      interface:  Sylius\Component\Contact\Model\RequestInterface
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Component\Resource\Factory\Factory
                      form:
                          default: Sylius\Bundle\ContactBundle\Form\Type\RequestType
                  validation_groups:
                      default: [ sylius ]
              contact_topic:
                  classes:
                      model:      Sylius\Component\Contact\Model\Topic
                      interface:  Sylius\Component\Contact\Model\TopicInterface
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Component\Resource\Factory\Factory
                      form:
                          default: Sylius\Bundle\ContactBundle\Form\Type\TopicType
                  validation_groups:
                      default: [ sylius ]
                  translation:
                      classes:
                          model:      Sylius\Component\Contact\Model\TopicTranslation
                          interface:  Sylius\Component\Contact\Model\TopicTranslationInterface
                          controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                          repository: ~
                          factory:    Sylius\Component\Resource\Factory\Factory
                          form:
                              default: Sylius\Bundle\ContactBundle\Form\Type\TopicTranslationType
                      validation_groups:
                          default: [ sylius ]
