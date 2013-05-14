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
            | username | password | enabled |
            | john     | foo      | yes     |
            | rick     | bar      | yes     |
          And the following zones are defined:
            | name  | type    | members        |
            | UK    | country | United Kingdom |
          And the following shipping methods exist:
            | zone | name        |
            | UK   | DHL Express |
          And the following payment methods exist:
            | name        | gateway | enabled |
            | Credit Card | stripe  | yes     |
          And I added product "PHP Top" to cart
          And I go to the checkout start page

    Scenario: Trying to sign in with bad credentials
              during the checkout
         When I fill in the following:
            | Username | john     |
            | Password | habababa |
          And I press "Login"
         Then I should see "Bad credentials"

    Scenario: Signing in during the checkout
         When I fill in the following:
            | Username | john |
            | Password | foo  |
          And I press "Login"
         Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the checkout
         When I fill in the following:
            | fos_user_registration_form_username             | mike             |
            | fos_user_registration_form_email                | mike@example.com |
            | fos_user_registration_form_plainPassword_first  | mikepass         |
            | fos_user_registration_form_plainPassword_second | mikepass         |
          And I press "Register"
         Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the checkout
         When I fill in the following:
            | fos_user_registration_form_username             | mike             |
            | fos_user_registration_form_email                | mike@example.com |
            | fos_user_registration_form_plainPassword_first  | mikepass         |
            | fos_user_registration_form_plainPassword_second | mikepass         |
          And I press "Register"
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Credit Card" radio button
          And I press "Continue"
         When I click "Place order"
         Then I should be on the store homepage
          And I should see "Thank you for your order!"
