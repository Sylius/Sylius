@inventory
Feature: Inventory tracking
    In order track and control my inventory
    As a store owner
    I want to be able to manage stock levels and availability

    Background:
        Given I am logged in as administrator
          And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |
          And the following products exist:
            | name           | price | options                     |
            | Super T-Shirt  | 19.99 | T-Shirt size, T-Shirt color |
            | Black T-Shirt  | 19.99 | T-Shirt size                |
            | Mug            | 5.99  |                             |
            | Sticker        | 10.00 |                             |

    Scenario: Seeing index of inventory
        Given I am on the dashboard page
         When I follow "Inventory levels"
         Then I should be on the inventory index page
          And I should see 6 stockables in the list

    Scenario: Seeing empty index of inventory
        Given there are no products
         When I am on the inventory index page
         Then I should see "There are no products to display."

    Scenario: Updating product stock level
        Given I am editing product "Sticker"
         When I fill in "Current stock" with "10"
          And I press "Save changes"
         Then I should be on the page of product "Sticker"
          And I should see "Product has been successfully updated."

    Scenario: Making product not available on demand
        Given I am editing product "Sticker"
         When I uncheck "Available on demand"
          And I press "Save changes"
         Then I should be on the page of product "Sticker"
          And I should see "Product has been successfully updated."

    Scenario: Updating variant stock level
        Given product "Black T-Shirt" is available in all variations
          And I am on the page of product "Black T-Shirt"
         When I click "edit" near "T-Shirt size: L"
          And I fill in "Current stock" with "10"
          And I press "Save changes"
         Then I should be on the page of product "Black T-Shirt"
          And I should see "Variant has been successfully updated."

    Scenario: Making variant not available on demand
        Given product "Black T-Shirt" is available in all variations
          And I am on the page of product "Black T-Shirt"
         When I click "edit" near "T-Shirt size: L"
          And I uncheck "Available on demand"
          And I press "Save changes"
         Then I should be on the page of product "Black T-Shirt"
          And I should see "Variant has been successfully updated."
