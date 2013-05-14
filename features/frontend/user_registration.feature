Feature: User registration
    In order to order products
    As a visitor
    I need to be able to create an account in the store

    Background:
        Given there are following users:
            | username | password |
            | bar      | foo      |

    Scenario: Successfully creating account in store
        Given I am on the store homepage
          And I follow "Register"
         When I fill in the following:
            | Email        | foo@bar.com |
            | Username     | foo         |
            | Password     | bar         |
            | Verification | bar         |
        And I press "Register"
       Then I should see "Welcome"
        And I should see "Logout"

    Scenario: Trying to register with non verified password
        Given I am on the store homepage
          And I follow "Register"
         When I fill in the following:
            | Email        | foo@bar.com |
            | Username     | foo         |
            | Password     | bar         |
            | Verification | foo         |
        And I press "Register"
       Then I should be on registration page
        And I should see "The entered passwords don't match"

    Scenario: Creating account during the checkout
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
            | name        | gateway | enabled |
            | Credit Card | stripe  | yes     |
          And I added product "PHP Top" to cart
          And I go to the checkout start page
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
