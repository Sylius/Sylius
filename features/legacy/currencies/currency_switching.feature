@legacy @currency
Feature: Currency selection
    In order to pay using my preferred currency
    As a customer
    I want to to select my currency in storefront

    Background:
        Given there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > PHP T-Shirts[TX2] |
        And the following products exist:
            | name    | price | taxons       |
            | PHP Top | 5.99  | PHP T-Shirts |
        And there are following currencies configured:
            | code | exchange rate | enabled |
            | EUR  | 1.00000       | yes     |
            | USD  | 0.76496       | yes     |
            | GBP  | 1.13986       | yes     |
            | PLN  | 1.01447       | no      |
        And there are following channels configured:
            | code        | name            | currencies    | locales | url       |
            | DEFAULT-WEB | Default Channel | EUR, GBP, USD | en_US   | localhost |
        And all products are assigned to the default channel

    Scenario: Only enabled currencies are visible to the user
        Given I am on the store homepage
        Then I should see 3 available currencies
        And I should see product prices in "EUR"

    Scenario: Changing the currency converts the prices
            on the storefront
        Given I am on the store homepage
        When I change the currency to "GBP"
        Then I should see product prices in "GBP"

    Scenario: Switching the currency as a logged in customer
        Given I am logged in user
        And I am on the store homepage
        When I change the currency to "USD"
        Then I should see product prices in "USD"

    Scenario: Correct exchange rate is applied to products
        Given I am on the store homepage
        When I change the currency to "GBP"
        Then I should see "£6.83"

    Scenario: Correct exchange rate is applied to products
        Given I am on the store homepage
        When I change the currency to "GBP"
        Then I should see "£6.83"

    Scenario: Correct unit price is displayed in the cart
        Given I am on the store homepage
        And I change the currency to "GBP"
        And I added product "PHP Top" to cart, with quantity "1"
        When I go to the cart summary page
        Then I should see item with unit price "£6.83" in the list
