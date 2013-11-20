@shipping
Feature: Shipments
    In order to track shipments
    As a store owner
    I want to be able to list and view shipments

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
            | UK + Poland  | country | United Kingdom, Poland        |
            | USA          | country | USA                           |
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
          And there are following shipping categories:
            | name    |
            | Regular |
            | Heavy   |
          And the following shipping methods exist:
            | category | zone         | name        |
            | Regular  | USA          | FedEx       |
            | Heavy    | UK + Poland  | DHL         |
            |          | UK + Poland  | DHL Express |
          And the following orders were placed:
            | user              | address                                                | shipment |
            | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany | FedEx    |
            | lars@example.com  | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       | DHL      |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug     | 1        |
            | Sticker | 4        |

    Scenario: Seeing index of all shipments
        Given I am on the dashboard page
         When I follow "Shipments"
         Then I should be on the shipment index page
          And I should see 2 shipments in the list

    Scenario: Seeing empty index of shipments
        Given there are no shipments
          And I am on the shipment index page
         Then I should see "There are no shipments"

    Scenario: Deleting shipment
        Given I am on the shipment page with method "DHL"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the shipment index page
          And I should see "Shipment has been successfully deleted."

    @javascript
    Scenario: Deleting shipment with js modal
        Given I am on the shipment page with method "DHL"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the shipment index page
          And I should see "Shipment has been successfully deleted."

    Scenario: Deleted shipment disappears from the list
        Given I am on the shipment page with method "DHL"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the shipment index page
          And I should not see shipment with name "DHL" in that list

    @javascript
    Scenario: Deleted shipment disappears from the list with js modal
        Given I am on the shipment page with method "DHL"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the shipment index page
          And I should not see shipment with name "DHL" in that list

    Scenario: Deleting shipment from the list
        Given I am on the shipment index page
         When I click "delete" near "DHL"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the shipment index page
          And "Shipment has been successfully deleted." should appear on the page
          But I should not see shipment with name "DHL" in that list

    @javascript
    Scenario: Deleting shipment from the list with js modal
        Given I am on the shipment index page
         When I click "delete" near "DHL"
          And I click "delete" from the confirmation modal
         Then I should still be on the shipment index page
          And "Shipment has been successfully deleted." should appear on the page
          But I should not see shipment with name "DHL" in that list

    Scenario: Accessing shipment details page via list
        Given I am on the shipment index page
         When I click "details" near "DHL"
         Then I should be on the shipment page with method "DHL"

    Scenario: Displaying the shipment state on details page
        Given I am on the shipment page with method "DHL"
         Then I should see "ready"
