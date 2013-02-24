Feature: Users management
    In order to manager customers
    As a store owner
    I want to be able to list registered users

    Background:
        Given I am logged in as administrator
        And there are following users:
          | username | enabled |
          | john     | yes     |
          | beth     | no      |
          | martha   | yes     |
          | rick     | no      |
          | dale     | yes     |

    Scenario: Seeing index of all users
        Given I am on the dashboard page
         When I follow "Users"
         Then I should be on the user index page
          And I should see 6 users in the list

    Scenario: Seeing index of unconfirmed users
        Given I am on the user index page
         When I follow "Unconfirmed accounts"
         Then I should still be on the user index page
          But I should see 2 users in the list
