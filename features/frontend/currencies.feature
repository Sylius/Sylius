@currencies
Feature: Currencies
    In order to buy products paying in different currencies
    As a visitor or as a logged in user
    I need to be able to switch between multiple currencies

    Background:
        Given there are following taxonomies defined:
          | name     |
          | Category |
        And taxonomy "Category" has following taxons:
          | Clothing > PHP T-Shirts |
        And the following products exist:
          | name          | price | taxons       |
          | PHP Top       | 5.99  | PHP T-Shirts |
        And there are following exchange rates:
            | currency | rate    |
            | EUR      | 1       |
            | USD      | 0.76496 |
            | GBP      | 1.13986 |

    Scenario: Switching currency as visitor
        Given I am on the store homepage
         When I follow "£"
         Then I should see product prices in "£"
         When I follow "$"
         Then I should see product prices in "$"
         When I follow "€"
         Then I should see product prices in "€"

    Scenario: Switching currency as logged in user
        Given I am logged in user
          And I am on the store homepage
         When I follow "£"
         Then I should see product prices in "£"
         When I follow "$"
         Then I should see product prices in "$"
         When I follow "£"
         Then I should see product prices in "£"
