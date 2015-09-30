@inventory
Feature: Stock location management
    In order to track inventory of multiple stock locations
    As a store owner
    I want to be able to manage them

    Background:
        Given store has default configuration
          And there are stock locations:
            | name                | code      |
            | London Werehouse    | LONDON    |
            | Nashville Werehouse | NASHVILLE |
            | Warsaw Werehouse    | WARSAW    |
          And I am logged in as administrator

    Scenario: Seeing index of all stock locations
        Given I am on the dashboard page
         When I follow "Stock locations"
         Then I should be on the stock location index page
          And I should see 3 stock locations in the list

    Scenario: Names are listed in the index
        Given I am on the dashboard page
         When I follow "Stock locations"
         Then I should be on the stock location index page
          And I should see stock item with name "London Werehouse" in the list

    Scenario: Stock location codes are listed in the index
        Given I am on the dashboard page
         When I follow "Stock locations"
         Then I should be on the stock location index page
          And I should see stock item with code "LONDON" in the list

    Scenario: Seeing empty index of stock locations
        Given there are no stock locations
         When I am on the stock location index page
         Then I should see "There are no stock locations configured"
