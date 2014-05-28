@orders
Feature: Orders management
    In order to manage my sales
    As a store owner
    I want to be able to list, view, edit and create orders

    Background:
        Given I am logged in as administrator
        And there is default currency configured
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
        When I go to the order index page
        Then I should see 2 orders in the list
        And I should see order with total "€14.74" in the list

    Scenario: Seeing empty index of orders
        Given there are no orders
        When I am on the order index page
        Then I should see "You have no new orders"

    Scenario: Deleting the order
        Given I am viewing order with number "000000001"
        When I press "delete"
        Then I should be on the order index page
        And I should see "Order has been successfully deleted."

    Scenario: Deleting the order via list
        Given I am on the order index page
        When I press "delete" near "#000000001"
        Then I should be on the order index page
        And I should see "Order has been successfully deleted."

    Scenario: Order integrity is preserved after deleting a product
        Given I have deleted the product "Mug"
        And I go to the order index page
        When I click "details" near "#000000001"
        Then I should be viewing order with number "000000001"
        And I should see "Mug"
        And I should see "Total: €14.74"

    Scenario: Displaying correct total on order page
        When I am viewing order with number "000000002"
        Then I should see "Total: €56.57"

    Scenario: Displaying correct items total on order page
        When I am viewing order with number "000000002"
        Then I should see "Items total: €45.99"

    Scenario: Displaying correct tax total on order page
        When I am viewing order with number "000000002"
        Then I should see "Tax total: €10.58"
