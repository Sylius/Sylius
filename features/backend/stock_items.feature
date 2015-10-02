@inventory
Feature: Stock item management
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
        And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
          And the following products exist:
            | name           | price | options                     |
            | Super T-Shirt  | 19.99 | T-Shirt size, T-Shirt color |
          And product "Super T-Shirt" is available in all variations
          And product "Super T-Shirt" is available in "London Warehouse" with 3 on hand
          And I am logged in as administrator

    Scenario: Seeing index of all stock items for location
        Given I am on the stock location index page
         When I click "Inventory" near "London Warehouse"
         Then I should see 9 stock items in the list
