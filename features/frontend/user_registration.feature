@users
Feature: User registration
    In order to order products
    As a visitor
    I need to be able to create an account in the store

    Background:
        Given there are following users:
            | email       | password |
            | bar@bar.com | foo      |

    Scenario: Successfully creating account in store
        Given I am on the store homepage
          And I follow "Register"
         When I fill in the following:
            | First name   | John        |
            | Last name    | Doe         |
            | Email        | foo@bar.com |
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
            | Password     | bar         |
            | Verification | foo         |
          And I press "Register"
         Then I should be on registration page
          And I should see "The entered passwords don't match"

    Scenario: Trying to register with already existing email
        Given I am on the store homepage
          And I follow "Register"
         When I fill in the following:
            | Email        | bar@bar.com |
            | Password     | bar         |
            | Verification | bar         |
          And I press "Register"
         Then I should be on registration page
          And I should see "The email is already used"

    Scenario: Trying to register without first and last name
        Given I am on the store homepage
          And I follow "Register"
         When I fill in the following:
            | Email        | foo@bar.com  |
            | Password     | bar          |
            | Verification | bar          |
          And I press "Register"
         Then I should be on registration page
          And I should see "Please enter your first name"
          And I should see "Please enter your last name"
