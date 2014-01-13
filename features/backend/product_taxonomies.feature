@products
Feature: Browsing products by taxonomies
    In order to manage my products efficiently
    As a store owner
    I want to be able to view them by category

    Background:
        Given I am logged in as administrator
          And there are following taxonomies defined:
            | name     |
            | Category |
            | Special  |
          And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts |
            | Clothing > Shorts   |
          And taxonomy "Special" has following taxons:
            | Featured |
            | New      |
          And the following products exist:
            | name          | price | taxons   |
            | Super T-Shirt | 19.99 | T-Shirts |
            | Black T-Shirt | 19.99 | T-Shirts |
            | Shorts        | 35.99 | Shorts   |
            | Bambi Shorts  | 35.00 | Shorts   |

    Scenario: Seeing index of all products for given taxonomy
        Given I am on the taxonomy index page
          And I follow "Category"
         When I click "Browse products" near "T-Shirts"
         Then I should see 2 products in the list

    Scenario: Category does not contain any products
        Given I am on the taxonomy index page
          And I follow "Special"
         When I click "Browse products" near "Featured"
         Then I should see "There are no products to display."
