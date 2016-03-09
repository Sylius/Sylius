@legacy @checkout
Feature: Checkout Payment
    In order to submit a payment
    As a logged in user
    I want to be able to use checkout payment step

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > PHP T-Shirts[TX2] |
        And the following products exist:
            | name    | price | taxons       |
            | PHP Top | 5.99  | PHP T-Shirts |
        And the following zones are defined:
            | name | type    | members        |
            | UK   | country | United Kingdom |
        And the following shipping methods exist:
            | code | zone | name        |
            | SM1  | UK   | DHL Express |
        And the following payment methods exist:
            | code | name        | gateway    | enabled |
            | PM1  | Credit Card | stripe     | yes     |
            | PM2  | PayPal      | paypal     | yes     |
            | PM3  | PayPal PRO  | paypal_pro | no      |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    | payment                         | shipping    |
            | Category | PayPal, PayPal PRO, Credit Card | DHL Express |
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
