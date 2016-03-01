@legacy @checkout
Feature: Checkout security
    In order to authenticate
    As a visitor
    I want to login or register during checkout

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
        And there are following users:
            | email            | password | enabled |
            | john@example.com | foo1     | yes     |
            | rick@example.com | bar1     | yes     |
        And the following zones are defined:
            | name | type    | members        |
            | UK   | country | United Kingdom |
        And the following shipping methods exist:
            | code | zone | name        |
            | SM1  | UK   | DHL Express |
        And the following payment methods exist:
            | code | name    | gateway | enabled |
            | PM1  | Offline | offline | yes     |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    | payment | shipping    |
            | Category | Offline | DHL Express |
        And I added product "PHP Top" to cart
        And I go to the checkout start page

    Scenario: Trying to sign in with bad credentials
            during the checkout
        When I fill in the following:
            | Email    | john@example.com |
            | Password | habababa         |
        And I press "Login"
        Then I should see "Invalid credentials"

    Scenario: Signing in during the checkout
        When I fill in the following:
            | Email    | john@example.com |
            | Password | foo1             |
        And I press "Login"
        Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the checkout
        When I fill in the following:
            | sylius_customer_registration_firstName                 | Mike             |
            | sylius_customer_registration_lastName                  | Small            |
            | sylius_customer_registration_email                     | mike@example.com |
            | sylius_customer_registration_user_plainPassword_first  | mikepass         |
            | sylius_customer_registration_user_plainPassword_second | mikepass         |
        And I press "Register"
        Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the whole checkout
        When I fill in the following:
            | sylius_customer_registration_firstName                 | Mike             |
            | sylius_customer_registration_lastName                  | Small            |
            | sylius_customer_registration_email                     | mike@example.com |
            | sylius_customer_registration_user_plainPassword_first  | mikepass         |
            | sylius_customer_registration_user_plainPassword_second | mikepass         |
        And I press "Register"
        And I fill in the shipping address to United Kingdom
        And I press "Continue"
        And I select the "DHL Express" radio button
        And I press "Continue"
        And I select the "Offline" radio button
        And I press "Continue"
        And I click "Place order"
        Then I should see "Thank you"

    Scenario: Creating account without first and last name
        When I fill in the following:
            | sylius_customer_registration_email                     | mike@example.com |
            | sylius_customer_registration_user_plainPassword_first  | mikepass         |
            | sylius_customer_registration_user_plainPassword_second | mikepass         |
        And I press "Register"
        Then I should be on the checkout security forward step
        And I should see "Please enter your first name"
        And I should see "Please enter your last name"

    Scenario: Creating account without email
        When I fill in the following:
            | sylius_customer_registration_firstName                 | Mike     |
            | sylius_customer_registration_lastName                  | Small    |
            | sylius_customer_registration_user_plainPassword_first  | mikepass |
            | sylius_customer_registration_user_plainPassword_second | mikepass |
        And I press "Register"
        Then I should be on the checkout security forward step
        And I should see "Please enter your email"
