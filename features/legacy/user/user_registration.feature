@legacy @user
Feature: User registration
    In order to order products
    As a visitor
    I need to be able to create an account in the store

    Background:
        Given store has default configuration
        And there are following users:
            | email       | password |
            | bar@bar.com | foo1     |
        And the following customers exist:
            | email              |
            | customer@email.com |
        And the following zones are defined:
            | name   | type    | members |
            | Poland | country | Poland  |
        And the following orders exist:
            | customer           | address                                        |
            | customer@email.com | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland |

    Scenario: Successfully creating account in store
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John        |
            | Last name    | Doe         |
            | Email        | foo@bar.com |
            | Password     | bar1        |
            | Verification | bar1        |
        And I press "Register"
        Then I should see "Welcome"
        And I should see "Logout"

    Scenario: Successfully creating account in store for existing customer
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John               |
            | Last name    | Doe                |
            | Email        | customer@email.com |
            | Password     | bar1               |
            | Verification | bar1               |
        And I press "Register"
        Then I should see "Welcome"
        And I should see "Logout"

    Scenario: Viewing orders placed as a guest after registration
        Given I registered with email "customer@email.com" and password "sylius"
        When I display my orders history
        Then I should see 1 order in the list

    Scenario: Viewing addresses used as a guest after registration
        Given I registered with email "customer@email.com" and password "sylius"
        When I display my address book
        Then I should see 1 address in the list
        And I should see "Poland"

    Scenario: Trying to register with non verified password
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John        |
            | Last name    | Doe         |
            | Email        | foo@bar.com |
            | Password     | bar1        |
            | Verification | foo2        |
        And I press "Register"
        Then I should be on registration page
        And I should see "The entered passwords don't match"

    Scenario: Trying to register without email
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John |
            | Last name    | Doe  |
            | Password     | bar1 |
            | Verification | bar1 |
        And I press "Register"
        Then I should be on registration page
        And I should see "Please enter your email"

    Scenario: Trying to register with already existing email
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John        |
            | Last name    | Doe         |
            | Email        | bar@bar.com |
            | Password     | bar1        |
            | Verification | bar1        |
        And I press "Register"
        Then I should be on registration page
        And I should see "This email is already used"

    Scenario: Trying to register with already existing non canonical email
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | First name   | John        |
            | Last name    | Doe         |
            | Email        | BaR@Bar.com |
            | Password     | bar1        |
            | Verification | bar1        |
        And I press "Register"
        Then I should be on registration page
        And I should see "This email is already used"

    Scenario: Trying to register without first and last name
        Given I am on the store homepage
        And I follow "Register"
        When I fill in the following:
            | Email        | foo@bar.com |
            | Password     | bar1        |
            | Verification | bar1        |
        And I press "Register"
        Then I should be on registration page
        And I should see "Please enter your first name"
        And I should see "Please enter your last name"
