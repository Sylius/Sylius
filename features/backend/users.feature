Feature: Users management
    In order to manager customers
    As a store owner
    I want to be able to list registered users

    Background:
        Given I am logged in as administrator
        And there are following users:
          | username | enabled |
          | beth     | no      |
          | martha   | yes     |
          | rick     | no      |
          | dale     | yes     |
        And the following zones are defined:
          | name   | type    | members |
          | Poland | country | Poland  |
        And the following orders were placed:
          | user | address                                        |
          | john | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland |

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

    Scenario: Accessing the user details page from users list
        Given I am on the user index page
         When I click "details" near "john"
         Then I should be on the page of user with username "john"
          And I should see 1 orders in the list
