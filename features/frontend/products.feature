@products
Feature: Products
    In order to know and pick the products
    As a visitor
    I want to be able to browse products

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
            | Clothing > Gloves       |
          And the following products exist:
            | name             | price | taxons       |
            | Super T-Shirt    | 19.99 | T-Shirts     |
            | Black T-Shirt    | 18.99 | T-Shirts     |
            | Sylius Tee       | 12.99 | PHP T-Shirts |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts |

    Scenario: Browsing products by taxon
        Given I am on the store homepage
         When I follow "T-Shirts"
         Then I should see there 2 products
          And I should see "Black T-Shirt"

    Scenario: Browsing products by taxon
        Given I am on the store homepage
         When I follow "PHP T-Shirts"
         Then I should see there 3 products
          And I should see "Sylius Tee"

    Scenario: Empty index of products
        Given there are no products
          And I am on the store homepage
         When I follow "Gloves"
         Then I should see "There are no products to display"

    Scenario: Accessing product page via "View more" button
        Given I am on the store homepage
          And I follow "T-Shirts"
         When I click "View more"
         Then I should be on the product page for "Super T-Shirt"

    Scenario: Accessing product page via title
        Given I am on the store homepage
          And I follow "PHP T-Shirts"
         When I click "Symfony T-Shirt"
         Then I should be on the product page for "Symfony T-Shirt"
