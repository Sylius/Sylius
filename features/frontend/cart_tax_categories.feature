@checkout
Feature: Tax categories
    In order to handle different types of merchandise
    As a store owner
    I want to apply taxes depending on the item cateogry

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
            | Food > Fruits |
          And the following zones are defined:
            | name  | type    | members        |
            | UK      | country | United Kingdom |
          And there are following tax categories:
            | name     |
            | Clothing |
            | Food     |
          And the following tax rates exist:
            | category | zone | name         | amount |
            | Clothing | UK   | Clothing VAT | 19%    |
            | Food     | UK   | Food VAT     | 7%     |
          And the following products exist:
            | name         | price | taxons       | tax category |
            | PHP Top      | 50    | PHP T-Shirts | Clothing     |
            | Golden Apple | 120   | Food         | Food         |

    Scenario: Correct taxes are applied for one item
        Given the default tax zone is "UK"
          And I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
         When I fill in "Quantity" with "2"
          And I press "Add to cart"
         Then I should be on the cart summary page
          And I should see "Clothing VAT (19%) €19.00"
          And "Tax total: €19.00" should appear on the page
          And "Grand total: €119.00" should appear on the page

    Scenario: Tax rates are applied accordingly to items of both categories
        Given the default tax zone is "UK"
          And I added product "PHP Top" to cart
          And I added product "Golden Apple" to cart
         When I go to the cart summary page
         Then I should see "Clothing VAT (19%) €9.50"
          And I should see "Food VAT (7%) €8.40"
          And "Tax total: €17.90" should appear on the page
          And "Grand total: €187.90" should appear on the page
