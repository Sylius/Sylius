Feature: Sign in to the store
    In order to view my orders list
    As a visitor
    I need to be able to log in to the store

    Background:
        Given there are following users:
            | username | password | enabled |
            | bar      | foo      | yes     |

    Scenario: Log in with username and password
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Login    | bar |
            | Password | foo |
        And I press "Login"
       Then I should be on the store homepage
        And I should see "Logout"

    Scenario: Log in with bad credentials
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Login    | bar |
            | Password | bar |
        And I press "Login"
       Then I should be on login page
        And I should see "Bad credentials"

    Scenario: Trying to login as non existing user
        Given I am on the store homepage
          And I follow "Login"
         When I fill in the following:
            | Login    | john |
            | Password | bar  |
        And I press "Login"
       Then I should be on login page
        And I should see "Bad credentials"
