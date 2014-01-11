@checkout
Feature: Checkout security
    In order to authenticate
    As a visitor
    I want to login or register during checkout

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following products exist:
            | name          | price | taxons       |
            | PHP Top       | 5.99  | PHP T-Shirts |
          And there are following users:
            | email            | password | enabled |
            | john@example.com | foo      | yes     |
            | rick@example.com | bar      | yes     |
          And the following zones are defined:
            | name  | type    | members        |
            | UK    | country | United Kingdom |
          And the following shipping methods exist:
            | zone | name        |
            | UK   | DHL Express |
          And the following payment methods exist:
            | name  | gateway | enabled |
            | Dummy | dummy   | yes     |
          And I added product "PHP Top" to cart
          And I go to the checkout start page

    Scenario: Trying to sign in with bad credentials
              during the checkout
         When I fill in the following:
            | Email    | john@example.com |
            | Password | habababa         |
          And I press "Login"
         Then I should see "Bad credentials"

    Scenario: Signing in during the checkout
         When I fill in the following:
            | Email    | john@example.com |
            | Password | foo              |
          And I press "Login"
         Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the checkout
         When I fill in the following:
            | fos_user_registration_form_email                | mike@example.com |
            | fos_user_registration_form_plainPassword_first  | mikepass         |
            | fos_user_registration_form_plainPassword_second | mikepass         |
            | fos_user_registration_form_firstName            | Mike             |
            | fos_user_registration_form_lastName             | Small            |
          And I press "Register"
         Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the whole checkout
         When I fill in the following:
            | fos_user_registration_form_email                | mike@example.com |
            | fos_user_registration_form_plainPassword_first  | mikepass         |
            | fos_user_registration_form_plainPassword_second | mikepass         |
            | fos_user_registration_form_firstName            | Mike             |
            | fos_user_registration_form_lastName             | Small            |
          And I press "Register"
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Dummy" radio button
          And I press "Continue"
          And I click "Place order"
         Then I should be on the store homepage
          And I should see "Thank you for your order!"

    Scenario: Creating account without first and last name
         When I fill in the following:
            | fos_user_registration_form_email                | mike@example.com |
            | fos_user_registration_form_plainPassword_first  | mikepass         |
            | fos_user_registration_form_plainPassword_second | mikepass         |
          And I press "Register"
         Then I should see "Please enter your first name"
          And I should see "Please enter your last name"
