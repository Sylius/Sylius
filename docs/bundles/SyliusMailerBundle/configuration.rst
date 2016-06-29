Configuration reference
=======================

.. code-block:: yaml

    sylius_mailer:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          sender_adapter: sylius.email_sender.adapter.swiftmailer # Adapter for sending e-mails.
          renderer_adapter: sylius.email_renderer.adapter.twig # Adapter for rendering e-mails.
          resources:
              email:
                  classes:
                      model:      Sylius\Mailer\Model\Email
                      interface:  Sylius\Mailer\Model\EmailInterface
                      controller: Sylius\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Resource\Factory\Factory
                      form:
                          default: Sylius\MailerBundle\Form\Type\EmailType
                          choice:  Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                  validation_groups:
                      default: [ sylius ]
          sender:
              name: # Required - default sender name.
              address: # Required - default sender e-mail address.
          templates: # Your templates available for selection in backend!
              label: Template path
              label: Template path
              label: Template path
          emails:
              your_email:
                  subject: Subject of your email
                  template: AppBundle:Email:yourEmail.html.twig
                  enabled: true/false
                  sender:
                     name: Custom name
                     address: Custom sender address for this e-mail
              your_another_email:
                  subject: Subject of your another email
                  template: AppBundle:Email:yourAnotherEmail.html.twig
                  enabled: true/false
                  sender:
                     name: Custom name
                     address: Custom sender address for this e-mail
