Feature: Users management
    In order to manager customers
    As a store owner
    I want to be able to list registered users

    Background:
        Given I am logged in as administrator
        And there are following users:
          | username | enabled |
          | foo      | yes     |
          | bar      | no      |
          | baz      | yes     |

    Scenario: Seeing index of all users
        Given I am on the dashboard page
         When I follow "Users"
         Then I should be on the user index page
          And I should see 4 users in the list

    Scenario: Seeing index of unconfirmed users
        Given I am on the dashboard page
         When I follow "Users"
          And I follow "Unconfirmed accounts"
         Then I should be on the user index page
          And I should see 1 users in the list

    Scenario: Seeing user details
        Given I am on the dashboard page
         When I follow "Users"
          And I follow "details"
          And I should see "User details"
