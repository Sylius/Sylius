@shipping
Feature: Shipments
    In order to track shipments
    As a store owner
    I want to be able to list and view shipments

    Background:
        Given store has default configuration
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
            | UK + Poland  | country | United Kingdom, Poland        |
            | USA          | country | United States                 |
          And the following shipping methods exist:
            | category | zone         | name        |
            | Regular  | USA          | FedEx       |
            | Heavy    | UK + Poland  | DHL         |
            |          | UK + Poland  | DHL Express |
          And the following orders were placed:
            | customer          | address                                                | shipment |
            | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany | FedEx    |
            | lars@example.com  | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       | DHL      |
          And I am logged in as administratorzs

    Scenario: Seeing index of all shipments
        Given I am on the dashboard page
         When I follow "Shipments"
         Then I should be on the shipment index page
          And I should see 2 shipments in the list

    Scenario: Seeing empty index of shipments
        Given there are no shipments
          And I am on the shipment index page
         Then I should see "There are no shipments"

    @javascript
    Scenario: Deleting shipment
        Given I am on the shipment page with method "DHL"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the shipment index page
          And I should see "Shipment has been successfully deleted."
          And I should not see shipment with name "DHL" in that list

    @javascript
    Scenario: Deleting shipment from the list
        Given I am on the shipment index page
         When I click "delete" near "DHL"
          And I click "delete" from the confirmation modal
         Then I should still be on the shipment index page
          And I should see "Shipment has been successfully deleted."
          And I should not see shipment with name "DHL" in that list

    Scenario: Accessing shipment details page via list
        Given I am on the shipment index page
         When I click "details" near "DHL"
         Then I should be on the shipment page with method "DHL"

    Scenario: Displaying the shipment state on details page
        Given I am on the shipment page with method "DHL"
         Then I should see "ready"
