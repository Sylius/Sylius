@checkout
Feature: Checkout Payment
    In order to submit a payment
    As a logged in user
    I want to be able to use checkout payment step

    Background:
        Given there is default currency configured
        And there is default channel configured
        And there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following products exist:
            | name          | price | taxons       |
            | PHP Top       | 5.99  | PHP T-Shirts |
          And the following zones are defined:
            | name  | type    | members        |
            | UK    | country | United Kingdom |
          And the following shipping methods exist:
            | zone | name        |
            | UK   | DHL Express |
          And the following payment methods exist:
            | name            | gateway    | enabled | calculator | calculator_configuration |
            | Credit Card     | stripe     | yes     | fixed      | amount: 0                |
            | Credit Card PRO | stripe     | yes     | percent    | percent: 0               |
            | PayPal          | paypal     | yes     | fixed      | amount: 50               |
            | PayPal PRO      | paypal_pro | no      | percent    | percent: 10              |
          And all products assigned to "DEFAULT-WEB" channel
          And channel "DEFAULT-WEB" has following configuration:
            | taxonomy | payment                                            | shipping    |
            | Category | PayPal, PayPal PRO, Credit Card, Credit Card PRO   | DHL Express |
          And I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button

    Scenario: Accessing payment checkout step
        Given I press "Continue"
         Then I should be on the checkout payment step

    Scenario: Only enabled payment methods are displayed
        Given I press "Continue"
         Then I should be on the checkout payment step
          And I should see "PayPal"
          And I should see "€0.50"
          But I should not see "PayPal PRO"
          And I should not see "€3.1"

    Scenario: Selecting one of payment methods
        Given I press "Continue"
         When I select the "PayPal" radio button
          And I press "Continue"
         Then I should be on the checkout finalize step

    Scenario: Showing payment fee charge
        Given I press "Continue"
         When I select the "PayPal" radio button
          And I press "Continue"
         Then I should be on the checkout finalize step
          And "Payment total: €0.50" should appear on the page
          And "Total: €31.49" should appear on the page

    Scenario: No fee is added if amount 0 is set in calculator
        Given I press "Continue"
         When I select the "Credit Card" radio button
          And I press "Continue"
         Then "Payment total: €0.00" should appear on the page
         When I click "Back"
          And I select the "Credit Card PRO" radio button
          And I press "Continue"
         Then I should see "Payment total: €0.00"
