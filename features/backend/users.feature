Feature: Users management
    In order to manager customers
    As a store owner
    I want to be able to list registered users

    Background:
        Given I am logged in as administrator
        And the following zones are defined:
          | name         | type    | members                       |
          | German lands | country | Germany, Austria, Switzerland |
        And there are following users:
          | email          | enabled | address                                                |
          | beth@foo.com   | no      | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
          | martha@foo.com | yes     | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
          | rick@foo.com   | no      | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
          | dale @foo.com  | yes     | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
        And the following zones are defined:
          | name   | type    | members |
          | Poland | country | Poland  |
        And the following orders were placed:
          | user            | address                                        |
          | orderko@foo.com | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland |

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

    Scenario: Searching for users
        Given I am on the user index page
         When I fill in "criteria_query" with "Klaus"
          And I press "Search"
         Then I should be on the user index page
          And I should see 2 users in the list

    Scenario: Accessing the user details page from users list
        Given I am on the user index page
         When I click "details" near "orderko@foo.com"
         Then I should be on the page of user with email "orderko@foo.com"
          And I should see 1 orders in the list

    Scenario: Accessing the user creation form
        Given I am on the user index page
          And I follow "create user"
         Then I should be on the user creation page

    Scenario: Submitting empty form
        Given I am on the user creation page
         When I press "Create"
         Then I should still be on the user creation page
          And I should see "Please enter a first name."

    Scenario: Creating user
        Given I am on the user creation page
         When I fill in the following:
            | First name | Saša               |
            | Last name  | Stamenković        |
            | Username   | umpirsky           |
            | Password   | Password           |
            | Email      | umpirsky@gmail.com |
          And I fill in the users shipping address to Germany
          And I press "Create"
         Then I should be on the page of user with email "umpirsky@gmail.com"
          And I should see "User has been successfully created."

    Scenario: Accessing the user editing form
        Given I am on the page of user with email "rick@foo.com"
         When I follow "edit"
         Then I should be editing user with email "rick@foo.com"

    Scenario: Accessing the editing form from the list
        Given I am on the user index page
         When I click "edit" near "rick"
         Then I should be editing user with email "rick@foo.com"

    Scenario: Updating the user
        Given I am editing user with email "rick@foo.com"
         When I fill in "Username" with "umpirsky"
          And I fill in "Email" with "umpirsky@gmail.com"
          And I press "Save changes"
         Then I should be on the page of user with email "umpirsky@gmail.com"
          And "User has been successfully updated." should appear on the page
          And "umpirsky@gmail.com" should appear on the page

    Scenario: Deleting user
        Given I am on the page of user with email "rick@foo.com"
         When I press "delete"
         Then I should be on the user index page
          And I should see "User has been successfully deleted."

    Scenario: Deleted user disappears from the list
        Given I am on the page of user with email "rick@foo.com"
         When I press "delete"
         Then I should be on the user index page
          And I should not see user with email "rick@foo.com" in that list

    Scenario: Deleting user from the list
        Given I am on the user index page
         When I click "delete" near "rick"
         Then I should still be on the user index page
          And "User has been successfully deleted." should appear on the page
          But I should not see user with email "rick@foo.com" in that list
