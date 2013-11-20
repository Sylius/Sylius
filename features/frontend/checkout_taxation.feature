@checkout
Feature: Checkout taxation
    In order to handle product taxation
    As a store owner
    I want to apply taxes during checkout

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following zones are defined:
            | name  | type    | members        |
            | UK    | country | United Kingdom |
          And there are following tax categories:
            | name          |
            | Taxable Goods |
          And the following tax rates exist:
            | category      | zone | name   | amount |
            | Taxable Goods | UK   | UK Tax | 15%    |
          And the following products exist:
            | name    | price | taxons       | tax category  |
            | PHP Top | 250   | PHP T-Shirts | Taxable Goods |
          And the following shipping methods exist:
            | zone | name        |
            | UK   | DHL Express |
          And the following payment methods exist:
            | name  | gateway | enabled |
            | Dummy | dummy   | yes     |
          And I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page

    Scenario: Placing the order
        Given I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Dummy" radio button
         When I press "Continue"
         Then I should be on the checkout finalize step
          And "Tax total: €37.50" should appear on the page
