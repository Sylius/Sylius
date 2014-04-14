@users
Feature: Sign in to the store
    In order to view my orders list
    As a visitor
    I need to be able to log in to the store

    Background:
        Given there are following users:
            | email        | username | password | enabled |
            | bar@foo.com  | bar      | foo      | yes     |
            | bar2@foo.com | bar2     | foo      | no     |

    Scenario: Log in with username and password
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar |
            | Password | foo |
          And I press "Login"
         Then I should be on the store homepage
          And I should see "Logout"

    Scenario: Log in with email and password
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar@foo.com |
            | Password | foo         |
          And I press "Login"
         Then I should be on the store homepage
          And I should see "Logout"

    Scenario: Log in with username and bad password
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar |
            | Password | bar         |
          And I press "Login"
         Then I should be on login page
          And I should see "Bad credentials"

    Scenario: Log in with email and bad password
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar@foo.com |
            | Password | bar         |
          And I press "Login"
         Then I should be on login page
          And I should see "Bad credentials"

    Scenario: Trying to login without credentials
        Given I am on the store homepage
          And I follow "Login"
         When I press "Login"
         Then I should be on login page
          And I should see "Bad credentials"

    Scenario: Trying to login as non existing username
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | john |
            | Password | bar  |
          And I press "Login"
         Then I should be on login page
          And I should see "Bad credentials"

    Scenario: Trying to login as non existing email
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | foo@bar.com |
            | Password | bar         |
          And I press "Login"
         Then I should be on login page
          And I should see "Bad credentials"

    Scenario: Trying to login with disabled username
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar2 |
            | Password | foo  |
          And I press "Login"
         Then I should be on login page
          And I should see "User account is disabled"

    Scenario: Trying to login with disabled email
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Email    | bar2@foo.com |
            | Password | foo          |
          And I press "Login"
         Then I should be on login page
          And I should see "User account is disabled"