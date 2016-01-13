@inventory
Feature: Stock location management
    In order to track inventory of multiple stock locations
    As a store owner
    I want to be able to manage them

    Background:
        Given store has default configuration
          And there are stock locations:
              | name                | code      |
              | London Warehouse    | LONDON    |
              | Nashville Warehouse | NASHVILLE |
              | Warsaw Warehouse    | WARSAW    |
          And I am logged in as administrator

    Scenario: Seeing index of all stock locations
        Given I am on the dashboard page
         When I follow "Stock locations"
         Then I should be on the stock location index page
          And I should see 3 stock locations in the list

    Scenario: Names and codes are listed in the index
        Given I am on the dashboard page
         When I follow "Stock locations"
         Then I should be on the stock location index page
          And I should see stock location with name "London Warehouse" in the list
          And I should see stock location with code "LONDON" in the list

    Scenario: Enabling stock location
        Given there is a disabled stock location "Warsaw Warehouse"
          And I am on the stock location index page
         When I click "Enable" near "Warsaw Warehouse"
         Then I should see enabled stock location with name "Warsaw Warehouse" in the list
          And I should see "The stock location has been successfully enabled."

    Scenario: Disabling stock location
        Given there is an enabled stock location "Warsaw Warehouse"
          And I am on the stock location index page
         When I click "Disable" near "Warsaw Warehouse"
         Then I should see disabled stock location with name "Warsaw Warehouse" in the list
          And I should see "The stock location has been successfully disabled."

    Scenario: Accessing the stock location creation form
        Given I am on the dashboard page
         When I follow "Stock locations"
          And I follow "Add Stock Location"
         Then I should be on the stock location creation page

    Scenario: Creating new stock location
        Given I am on the stock location creation page
         When I fill in "Name" with "Hong Kong"
         When I fill in "Code" with "HONG-KONG"
          And I press "Create"
         Then I should be on the stock location index page
          And I should see stock location with name "Hong Kong" in the list
          And I should see "The stock location has been successfully created."

    Scenario: Accessing the stock location editing form
        Given I am on the stock location index page
         When I click "edit" near "London Warehouse"
         Then I should be editing stock location "London Warehouse"

    Scenario: Updating the stock location
        Given I am on the stock location index page
          And I click "edit" near "London Warehouse"
         When I fill in "Name" with "Big Ben Warehouse"
          And I press "Save changes"
         Then I should be on the stock location index page
          And I should see stock location with name "Big Ben Warehouse" in the list
          And I should not see stock location with name "London Warehouse" in the list
          And I should see "The stock location has been successfully updated."

    Scenario: Submitting form without specifying the required values
        Given I am on the stock location creation page
         When I press "Create"
         Then I should still be on the stock location creation page
          And I should see "Please enter a code."
          And I should see "Please enter a name."

    Scenario: Cannot edit stock_location code
         When I am editing stock_location with code "LONDON"
         Then I should not be able to edit "Code" field

    Scenario: Try add stock location with existing code
        Given I am on the stock location creation page
          And I fill in "Code" with "LONDON"
          And I fill in "Name" with "Big Ben Warehouse"
         When I press "Create"
         Then I should still be on the stock location creation page
          And I should see "This code is already used."

    @javascript
    Scenario: Deleting a stock location
        Given I am on the stock location index page
         When I click "delete" near "London Warehouse"
          And I click "Delete" from the confirmation modal
         Then I should still be on the stock location index page
          And I should see "The stock location has been successfully deleted."
          And I should not see stock location with name "London Warehouse" in that list

    Scenario: Seeing empty index of stock locations
        Given there are no stock locations
         When I am on the stock location index page
         Then I should see "There are no stock locations configured."