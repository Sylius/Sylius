@checkout
Feature: Checkout Payment
    In order to submit a payment
    As a logged in user
    I want to be able to use checkout payment step

    Background:
        Given there are following taxonomies defined:
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
            | name        | gateway    | enabled |
            | Credit Card | stripe     | yes     |
            | PayPal      | paypal     | yes     |
            | PayPal PRO  | paypal_pro | no      |
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
          But I should not see "PayPal PRO"

    Scenario: Selecting one of payment methods
        Given I press "Continue"
         When I select the "PayPal" radio button
          And I press "Continue"
         Then I should be on the checkout finalize step
