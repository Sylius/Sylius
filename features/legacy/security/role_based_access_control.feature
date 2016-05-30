@legacy @security
Feature: Hierarchical Role based access control (HRBAC)
    In order to restrict access to certain functionality
    As a Store Owner
    I want to be able to allow only certain roles to access backend

    Background:
        Given store has default configuration
        And authorization checks are enabled
        And there is following permission hierarchy:
            | code                  | parent         | description             |
            | sylius.catalog        |                | Manage products catalog |
            | sylius.product.show   | sylius.catalog | View single product     |
            | sylius.product.index  | sylius.catalog | List all products       |
            | sylius.product.create | sylius.catalog | Add new products        |
            | sylius.product.update | sylius.catalog | Edit products           |
            | sylius.product.delete | sylius.catalog | Delete products         |
            | sylius.sales          |                | Manage products sales   |
            | sylius.order.show     | sylius.sales   | View single order       |
            | sylius.order.index    | sylius.sales   | List all orders         |
            | sylius.order.create   | sylius.sales   | Add new orders          |
            | sylius.order.update   | sylius.sales   | Edit orders             |
            | sylius.order.delete   | sylius.sales   | Delete orders           |
        And there is following role hierarchy:
            | code                   | parent               | name            | security roles             |
            | sylius.administrator   |                      | Administrator   | ROLE_ADMINISTRATION_ACCESS |
            | sylius.catalog_manager | sylius.administrator | Catalog Manager | ROLE_ADMINISTRATION_ACCESS |
            | sylius.sales_manager   | sylius.administrator | Sales Manager   | ROLE_ADMINISTRATION_ACCESS |
        And role "Catalog Manager" has the following permissions:
            | sylius.catalog |
        And role "Sales Manager" has the following permissions:
            | sylius.sales |

    Scenario: Only selected menus are visible for Sales Manager
        Given I am logged in as "Sales Manager"
        When I go to the dashboard page
        Then I should not see "Products" in the menu
        But I should see "Orders" in the menu

    Scenario: Only selected menus are visible for Catalog Manager
        Given I am logged in as "Catalog Manager"
        When I go to the dashboard page
        Then I should not see "Orders" in the menu
        But I should see "Products" in the menu

    Scenario: Sales Manager cannot list products
        Given I am logged in as "Sales Manager"
        When I go to the product index page
        Then I should have my access denied

    Scenario: Sales Manager can list orders
        Given I am logged in as "Sales Manager"
        When I go to the order index page
        Then I should see "You have no new orders"

    Scenario: Catalog Manager can list products
        Given I am logged in as "Catalog Manager"
        When I go to the product index page
        Then I should see "There are no products"

    Scenario: Catalog Manager can create products
        Given I am logged in as "Catalog Manager"
        And I am on the product creation page
        When I fill in the following:
            | Name                       | Book about Everything   |
            | Description                | Interesting description |
            | sylius_product_legacy_code | OMNI_BOOK               |
        And I press "Create"
        Then I should be on the page of product "Book about Everything"
        And I should see "Product has been successfully created"

    Scenario: Sales Manager cannot create products
        Given I am logged in as "Sales Manager"
        When I go to the product creation page
        Then I should have my access denied
