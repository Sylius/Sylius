@inventory
Feature: Stock item management
    In order to track inventory of multiple stock locations
    As a store owner
    I want to be able to manage them

    Background:
        Given store has default configuration
          And there are following options:
              | code | name         | presentation  | values                          |
              | O1   |T-Shirt color | Color         | Red[OV1], Blue[OV2], Green[OV3] |
              | O2   |T-Shirt size  | Size          | S[OV4], M[OV5], L[OV6]          |
          And the following products exist:
              | name           | price | options                     |
              | Super T-Shirt  | 19.99 | T-Shirt size, T-Shirt color |
          And product "Super T-Shirt" is available in all variations
          And there are stock locations:
              | name                | code      |
              | London Warehouse    | LONDON    |
              | Nashville Warehouse | NASHVILLE |
              | Warsaw Warehouse    | WARSAW    |
          And product "Super T-Shirt" is available in "London Warehouse" with 3 on hand
          And I am logged in as administrator

    Scenario: Seeing index of all stock items for location
        Given I am on the stock location index page
         When I click "Inventory" near "London Warehouse"
         Then I should see 10 stock items in the list
