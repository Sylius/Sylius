@orders
Feature: Orders management
    In order to manage my sales
    As a store owner
    I want to be able to list, view, edit and create orders

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
          And there are following tax categories:
            | name    |
            | General |
          And there are products:
            | name          | price | tax category |
            | Mug           | 5.99  | General      |
            | Sticker       | 10.00 | General      |
          And the following tax rates exist:
            | category | zone         | name | amount |
            | General  | German lands | VAT  | 23     |
          And the following orders were placed:
            | user              | address                                                |
            | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | lars@example.com  | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug     | 1        |
            | Sticker | 4        |

    Scenario: Seeing index of all orders
        Given I am on the dashboard page
         When I follow "Current orders"
         Then I should be on the order index page
          And I should see 2 orders in the list

    Scenario: Seeing empty index of orders
        Given there are no orders
         When I am on the order index page
         Then I should see "You have no new orders"

    Scenario: Displaying order total in the list
        Given I am on the dashboard page
         When I follow "Current orders"
         Then I should be on the order index page
          And I should see order with total "€14.74" in the list

    Scenario: Accessing the order creation form
        Given I am on the dashboard page
         When I follow "Current orders"
          And I follow "Create order"
         Then I should be on the order creation page

    Scenario: Accessing the order editing form
        Given I am viewing order with number "000000001"
         When I follow "edit"
         Then I should be editing order with number "000000001"

    Scenario: Accessing the editing form from the list
        Given I am on the order index page
         When I click "edit" near "#000000002"
         Then I should be editing order with number "000000002"

    Scenario: Deleting the order
        Given I am viewing order with number "000000001"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the order index page
          And I should see "Order has been successfully deleted."

    @javascript
    Scenario: Deleting the order with js modal
        Given I am viewing order with number "000000001"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the order index page
          And I should see "Order has been successfully deleted."

    Scenario: Deleting the order via list button
        Given I am on the order index page
         When I press "delete" near "#000000001"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the order index page
          And I should see "Order has been successfully deleted."

    @javascript
    Scenario: Deleting the order via list button with js modal
        Given I am on the order index page
         When I press "delete" near "#000000001"
          And I click "delete" from the confirmation modal
         Then I should be on the order index page
          And I should see "Order has been successfully deleted."

    Scenario: Deleted order disappears from the list
        Given I am viewing order with number "000000002"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the order index page
          And I should not see order with number "#000000002" in the list

    @javascript
    Scenario: Deleted order disappears from the list with js modal
        Given I am viewing order with number "000000002"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the order index page
          And I should not see order with number "#000000002" in the list

    Scenario: Order integrity is preserved after deleting a product
        Given I have deleted the product "Mug"
          And I go to the order index page
         When I click "details" near "#000000001"
         Then I should be viewing order with number "000000001"
          And I should see "Mug"
          And I should see "Total: €14.74"

    Scenario: Accessing the order details page from list
        Given I am on the order index page
         When I click "details" near "#000000001"
         Then I should be viewing order with number "000000001"

    Scenario: Accessing the order details page by clicking the number
        Given I am on the order index page
         When I click "#000000002"
         Then I should be viewing order with number "000000002"

    Scenario: Displaying correct total on order page
        Given I am viewing order with number "000000002"
         Then I should see "Total: €56.57"

    Scenario: Displaying correct items total on order page
        Given I am viewing order with number "000000002"
         Then I should see "Items total: €45.99"

    Scenario: Displaying correct tax total on order page
        Given I am viewing order with number "000000002"
         Then I should see "Tax total: €10.58"
