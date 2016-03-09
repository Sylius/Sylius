@legacy @security
Feature: Permissions management
    In order to restrict access to certain functionality
    As a Store Owner
    I want to be able to manage permissions

    Background:
        Given store has default configuration
        And there is following permission hierarchy:
            | code                     | parent         | description             |
            | sylius.catalog           |                | Manage products catalog |
            | sylius.product.show      | sylius.catalog | View single product     |
            | sylius.product.index     | sylius.catalog | List all products       |
            | sylius.product.create    | sylius.catalog | Add new products        |
            | sylius.product.update    | sylius.catalog | Edit products           |
            | sylius.product.delete    | sylius.catalog | Delete products         |
            | sylius.permission.show   |                | View single permission  |
            | sylius.permission.index  |                | List all permissions    |
            | sylius.permission.create |                | Add new permissions     |
            | sylius.permission.update |                | Edit permissions        |
            | sylius.permission.delete |                | Delete permissions      |
        And there is following role hierarchy:
            | code                 | parent | name          | security roles             |
            | sylius.administrator |        | Administrator | ROLE_ADMINISTRATION_ACCESS |
        And role "Administrator" has the following permissions:
            | sylius.permission.show   |
            | sylius.permission.index  |
            | sylius.permission.create |
            | sylius.permission.update |
            | sylius.permission.delete |
        And I am logged in as administrator

    Scenario: Seeing index of all permissions
        Given I am on the dashboard page
        When I follow "Permissions"
        Then I should be on the permission index page
        And I should see 12 permissions in the list

    Scenario: Permission is validated
        Given I am on the permission creation page
        When I press "Create"
        Then I should still be on the permission creation page
        And I should see "Please enter code"

    Scenario: Permission code must be unique
        Given I am on the permission creation page
        When I fill in "Code" with "sylius.product.index"
        And I press "Create"
        Then I should still be on the permission creation page
        And I should see "Code must be unique"

    Scenario: Creating new permission under tree
        Given I am on the permission creation page
        When I fill in "Code" with "sylius.product.display_sales_stats"
        And I fill in "Description" with "View sales statistics"
        And I select "Manage products catalog" from "Parent"
        And I press "Create"
        Then I should be on the permission index page
        And I should see "Permission has been successfully created"
        And I should see 13 permissions in the list
        And I should see permission with code containing "sylius.product.display_sales_stats" in that list

    Scenario: Cannot edit the parent of root node
        Given I am on the permission index page
        When I click "Edit" near "root"
        Then I should not see "Parent"

    Scenario: Updating the permission
        Given I am on the permission index page
        And I click "Edit" near "sylius.product.index"
        When I fill in "Description" with "Browse all product lists"
        And I press "Save changes"
        Then I should be on the permission index page
        And I should see "Permission has been successfully updated"
        And I should see "Browse all product lists"
        But I should not see "List all products"

    Scenario: Deleting permission
        Given I am on the permission index page
        When I click "Delete" near "Edit product"
        Then I should be on the permission index page
        And I should see "Permission has been successfully deleted"
        And I should not see permission with code containing "sylius.product.edit" in the list
