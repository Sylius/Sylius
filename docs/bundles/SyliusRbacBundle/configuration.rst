Configuration reference
=======================

.. code-block:: yaml

    sylius_rbac:
          driver: ~ # The driver used for persistence layer. Currently only `doctrine/orm` is supported.
          authorization_checker: sylius.authorization_checker.default
          identity_provider: sylius.authorization_identity_provider.security
          permission_map: sylius.permission_map.cached

          security_roles:
                ROLE_ADMINISTRATION_ACCESS: Can access backend

          roles:
                app.admin:
                    name: Administrator
                app.cash_manager:
                    name: Cash Manager
                    description: People responsible for managing money.
                    permissions: [app.view_cash, app.add_cash, app.remove_cash]
                    security_roles: [ROLE_ADMINISTRATION_ACCESS]
          roles_hierarchy:
                app.admin: [app.cash_manager]

          permissions:
                app.view_cash: View cash
                app.add_cash: Add cash
                app.remove_cash: Remove cash
                app.manage_cash: Manage cash
          permissions_hierarchy:
                app.manage_cash: [app.view_cash, app.add_cash, app.remove_cash]

          resources:
              role:
                  classes:
                      model:      Sylius\Component\Rbac\Model\Role
                      interface:  Sylius\Component\Rbac\Model\RoleInterface
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      repository: ~
                      factory:    Sylius\Component\Resource\Factory\Factory
                      form:
                            default: Sylius\Bundle\RbacBundle\Form\Type\RoleType
                            choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                  validation_groups:
                    default: [ sylius ]
              permission:
                  classes:
                      model:      Sylius\Component\Rbac\Model\Permission
                      interface:  Sylius\Component\Rbac\Model\PermissionInterface
                      repository: ~
                      controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                      factory:    Sylius\Component\Resource\Factory\Factory
                      form:
                            default: Sylius\Bundle\RbacBundle\Form\Type\PermissionType
                            choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                  validation_groups:
                    default: [ sylius ]
