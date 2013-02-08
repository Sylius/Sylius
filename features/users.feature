Feature: Users management
    As a store owner
    I want to be able to list registered users
    In order to see user details

    Background:
        Given I am logged in as administrator
        And there are following users:
          | username | enabled |
          | foo      | 1       |
          | bar      | 0       |
          | baz      | 1       |

    Scenario: Seeing index of all users
        Given I am on the dashboard page
         When I follow "Users"
         Then I should be on the user index page
          And I should see 4 users in the list

    Scenario: Seeing index of unconfirmed users
        Given I am on the dashboard page
         When I follow "Users"
          And I follow "unconfirmed users"
         Then I should be on the user index page
          And I should see 1 users in the list

    Scenario: Seeing user details
        Given I am on the dashboard page
         When I follow "Users"
          And I follow "details"
          And I should see "User details"
