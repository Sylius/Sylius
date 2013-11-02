@users
Feature: User groups management
    In order to manage customers
    As a store owner
    I want to be able to group them

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
          And there are following groups:
            | name                | roles             |
            | Wholesale Customers | ROLE_WHOLESALE    |
            | Retail Customers    | ROLE_RETAIL       |
            | Administrators      | ROLE_SYLIUS_ADMIN |
            | Sales               | ROLE_SYLIUS_SALES |
          And there are following users:
            | email              | enabled | groups                | address                                                |
            | marc@example.com   | yes     | Administrators, Sales | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | jane@example.com   | yes     | Sales                 | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
            | beth@example.com   | no      | Wholesale Customers   | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | martha@example.com | yes     | Retail Customers      |                                                        |
            | rick@example.com   | no      | Retail Customers      | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
            | dale@example.com   | yes     | Wholesale Customers   |                                                        |

    Scenario: Seeing index of all groups
        Given I am on the dashboard page
         When I follow "Groups"
         Then I should be on the group index page
          And I should see 4 groups in the list

    Scenario: Accessing the group creation form
        Given I am on the group index page
          And I follow "Create group"
         Then I should be on the group creation page

    Scenario: Submitting empty form
        Given I am on the group creation page
         When I press "Create"
         Then I should still be on the group creation page
          And I should see "Please enter group name."

    Scenario: Creating group
        Given I am on the group creation page
         When I fill in the following:
            | Name  | Dealers                   |
            | Roles | ROLE_DEALER, ROLE_MANAGER |
          And I press "Create"
         Then I should be on the group index page
          And I should see group with name "Dealers" in the list

    Scenario: Selecting the groups for user
        Given I am editing user with username "rick@example.com"
         When I select "Retail Customers" from "Groups"
          And I press "Save changes"
         Then I should be on the page of user with username "rick@example.com"
          And "User has been successfully updated." should appear on the page

    Scenario: Accessing the editing form from the list
        Given I am on the group index page
         When I click "edit" near "Retail Customers"
         Then I should be editing group with name "Retail Customers"

    Scenario: Updating the group
        Given I am editing group with name "Wholesale Customers"
         When I fill in "Name" with "Premium Customers"
          And I press "Save changes"
         Then I should be on the group index page
          And "Group has been successfully updated." should appear on the page
          And "Premium Customers" should appear on the page
          But I should not see "Wholesale Customers"

    Scenario: Deleting group
        Given I am on the group index page
         When I click "delete" near "Wholesale Customers"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the group index page
          And I should see "Group has been successfully deleted."
          And I should not see group with name "Wholesale Customers" in that list

    @javascript
    Scenario: Deleting group with js modal
        Given I am on the group index page
         When I click "delete" near "Wholesale Customers"
          And I click "delete" from the confirmation modal
         Then I should still be on the group index page
          And I should see "Group has been successfully deleted."
          And I should not see group with name "Wholesale Customers" in that list
