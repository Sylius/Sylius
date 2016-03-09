@legacy @user
Feature: Sign in to the store
    In order to view my orders list
    As a visitor
    I need to be able to log in to the store

    Background:
        Given store has default configuration
        And there are following users:
            | email       | password | enabled |
            | bar@foo.com | foo1     | yes     |

    Scenario: Log in with username and password
        Given I am on the store homepage
        And I follow "Login"
        When I fill in the following:
            | Email    | bar@foo.com |
            | Password | foo1        |
        And I press "Login"
        Then I should be redirected to the store homepage
        And I should see "Logout"

    Scenario: Log in with bad credentials
        Given I am on user login page
        When I fill in the following:
            | Email    | bar@foo.com |
            | Password | bar1        |
        And I press "Login"
        Then I should still be on user login page
        And I should see "Invalid credentials"

    Scenario: Trying to login without credentials
        Given I am on user login page
        When I press "Login"
        Then I should still be on user login page
        And I should see "Invalid credentials"

    Scenario: Trying to login as non existing user
        Given I am on user login page
        When I fill in the following:
            | Email    | john |
            | Password | bar1 |
        And I press "Login"
        Then I should still be on user login page
        And I should see "Invalid credentials"
