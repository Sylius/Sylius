@legacy @product
Feature: Browsing products by taxons
    In order to manage my products efficiently
    As a store owner
    I want to be able to view them by category

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | TRX1 | Category |
            | TRX2 | Special  |
        And taxon "Category" has following children:
            | Clothing[TX1] > T-Shirts[TX2] |
            | Clothing[TX1] > Shorts[TX3]   |
        And taxon "Special" has following children:
            | Featured[TX4] |
            | New[TX5]      |
        And the following products exist:
            | name          | price | taxons   |
            | Super T-Shirt | 19.99 | T-Shirts |
            | Black T-Shirt | 19.99 | T-Shirts |
            | Shorts        | 35.99 | Shorts   |
            | Bambi Shorts  | 35.00 | Shorts   |
        And I am logged in as administrator

    Scenario: Seeing index of all products for given taxon
        Given I am on the taxon index page
        And I follow "Category"
        When I click "Browse products" near "T-Shirts"
        Then I should see 2 products in the list

    Scenario: Category does not contain any products
        Given I am on the taxon index page
        And I follow "Special"
        When I click "Browse products" near "Featured"
        Then I should see "There are no products to display"
