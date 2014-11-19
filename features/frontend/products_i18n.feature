@products
Feature: Browsing frontend with translated products
    In order to be able to understand the product being sold in the store
    As a visitor
    I want to be able to browse products in my preferred language

    Background:
        Given there is default currency configured
        And there are following taxonomies defined:
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
        And there are following locales configured:
            | code  | enabled |
            | de_DE | yes       |
            | en_US | no        |
            | es    | yes       |
        And the following product translations exist:
            | product    | translation  | code |
            | Sylius Tee | Camiseta Tee | es   |

    Scenario: Browsing products by taxon
        Given I am on the store homepage
        When I change the locale to "Spanish"
        Then I should see "Camiseta Tee"