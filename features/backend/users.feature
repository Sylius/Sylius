@users
Feature: Users management
    In order to manager customers
    As a store owner
    I want to be able to list registered users

    Background:
        Given I am logged in as administrator
          And there are products:
            | name | price |
            | Mug  | 5.99  |
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
            | user | address                                        |
            | john | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |

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
         When I click "details" near "john"
         Then I should be on the page of user with username "john"
          And I should see 1 orders in the list

    Scenario: Accessing the user creation form
        Given I am on the user index page
          And I follow "create user"
         Then I should be on the user creation page

    Scenario: Submitting empty form
        Given I am on the user creation page
         When I press "Create"
         Then I should still be on the user creation page
          And I should see "Please enter your first name."

    Scenario: Creating user
        Given I am on the user creation page
         When I fill in the following:
            | First name | Saša               |
            | Last name  | Stamenković        |
            | Password   | Password           |
            | Email      | umpirsky@gmail.com |
          And I press "Create"
         Then I should be on the page of user with username "umpirsky@gmail.com"
          And I should see "User has been successfully created."

    Scenario: Accessing the user editing form
        Given I am on the page of user with username "rick@foo.com"
         When I follow "edit"
         Then I should be editing user with username "rick@foo.com"

    Scenario: Accessing the editing form from the list
        Given I am on the user index page
         When I click "edit" near "rick@foo.com"
         Then I should be editing user with username "rick@foo.com"

    Scenario: Updating the user
        Given I am editing user with username "rick@foo.com"
         When I fill in "Email" with "umpirsky@gmail.com"
          And I press "Save changes"
         Then I should be on the page of user with username "umpirsky@gmail.com"
          And "User has been successfully updated." should appear on the page
          And "umpirsky@gmail.com" should appear on the page

    Scenario: Deleting user
        Given I am on the page of user with username "rick@foo.com"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the user index page
          And I should see "User has been successfully deleted."

    @javascript
    Scenario: Deleting user with js modal
        Given I am on the page of user with username "rick@foo.com"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the user index page
          And I should see "User has been successfully deleted."

    Scenario: Deleted user disappears from the list
        Given I am on the page of user with username "rick@foo.com"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the user index page
          And I should not see user with username "rick@foo.com" in that list

    @javascript
    Scenario: Deleted user disappears from the list with js modal
        Given I am on the page of user with username "rick@foo.com"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the user index page
          And I should not see user with username "rick@foo.com" in that list

    Scenario: Deleting user from the list
        Given I am on the user index page
         When I click "delete" near "rick@foo.com"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the user index page
          And "User has been successfully deleted." should appear on the page
          But I should not see user with username "rick@foo.com" in that list

    @javascript
    Scenario: Deleting user from the list with js modal
        Given I am on the user index page
         When I click "delete" near "rick@foo.com"
          And I click "delete" from the confirmation modal
         Then I should still be on the user index page
          And "User has been successfully deleted." should appear on the page
          But I should not see user with username "rick@foo.com" in that list
