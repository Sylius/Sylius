@legacy @security
Feature: Roles management
    In order to restrict access to certain functionality
    As a Store Owner
    I want to be able to manage user roles

    Background:
        Given store has default configuration
        And there is following permission hierarchy:
            | code                  | parent         | description             |
            | sylius.catalog        |                | Manage products catalog |
            | sylius.product.show   | sylius.catalog | View single product     |
            | sylius.product.index  | sylius.catalog | List all products       |
            | sylius.product.create | sylius.catalog | Add new products        |
            | sylius.product.update | sylius.catalog | Edit products           |
            | sylius.product.delete | sylius.catalog | Delete products         |
            | sylius.role.show      |                | View single role        |
            | sylius.role.index     |                | List all roles          |
            | sylius.role.create    |                | Add new roles           |
            | sylius.role.update    |                | Edit roles              |
            | sylius.role.delete    |                | Delete roles            |
        And there is following role hierarchy:
            | code                   | parent               | name            | security roles             |
            | sylius.administrator   |                      | Administrator   | ROLE_ADMINISTRATION_ACCESS |
            | sylius.catalog_manager | sylius.administrator | Catalog Manager | ROLE_ADMINISTRATION_ACCESS |
        And role "Administrator" has the following permissions:
            | sylius.role.show   |
            | sylius.role.index  |
            | sylius.role.create |
            | sylius.role.update |
            | sylius.role.delete |
        And role "Catalog Manager" has the following permissions:
            | sylius.catalog |
        And I am logged in as administrator

    Scenario: Seeing index of all roles
        Given I am on the dashboard page
        When I follow "Roles"
        Then I should be on the role index page
        And I should see 3 roles in the list

    Scenario: Role is validated
        Given I am on the role creation page
        When I press "Create"
        Then I should still be on the role creation page
        And I should see "Please enter code"
        And I should see "Please enter role name"

    Scenario: Role code must be unique
        Given I am on the role creation page
        When I fill in "Code" with "sylius.catalog_manager"
        And I press "Create"
        Then I should still be on the role creation page
        And I should see "Code must be unique"

    Scenario: Creating new role under tree
        Given I am on the role creation page
        When I fill in "Code" with "sylius.salesman"
        And I fill in "Name" with "Salesman"
        And I select "Administrator" from "Parent"
        And I press "Create"
        Then I should be on the role index page
        And I should see "Role has been successfully created"
        And I should see 4 roles in the list
        And I should see role with name "Salesman" in that list

    Scenario: Cannot edit the parent of root node
        Given I am on the role index page
        When I click "Edit" near "root"
        Then I should not see "Parent"

    Scenario: Updating the role
        Given I am on the role index page
        And I click "Edit" near "sylius.catalog_manager"
        When I fill in "Description" with "Manage products catalog"
        And I press "Save changes"
        Then I should be on the role index page
        And I should see "Role has been successfully updated"
        And I should see "Manage products catalog"

    Scenario: Deleting role
        Given I am on the role index page
        When I click "Delete" near "sylius.catalog_manager"
        Then I should be on the role index page
        And I should see "Role has been successfully deleted"
        And I should not see role with name "Catalog Manager" in the list
