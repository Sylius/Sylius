@shipping
Feature: Shipping categories
    In order to limit products to certain shipping methods
    As a store owner
    I want to be able to manage shipping categories

    Background:
        Given I am logged in as administrator
          And there are following shipping categories:
            | name    |
            | Regular |
            | Heavy   |

    Scenario: Seeing index of all shipping categories
        Given I am on the dashboard page
         When I follow "Shipping categories"
         Then I should be on the shipping category index page
          And I should see 2 shipping categories in the list

    Scenario: Seeing empty index of shipping categories
        Given there are no shipping categories
          And I am on the shipping category index page
         Then I should see "There are no shipping categories configured"

    Scenario: Accessing the shipping category creation form
        Given I am on the dashboard page
         When I follow "Shipping categories"
          And I follow "Create shipping category"
         Then I should be on the shipping category creation page

    Scenario: Submitting invalid form without name
        Given I am on the shipping category creation page
         When I press "Create"
         Then I should still be on the shipping category creation page
          And I should see "Please enter shipping category name"

    Scenario: Creating new shipping category
        Given I am on the shipping category creation page
         When I fill in "Name" with "Light"
          And I press "Create"
         Then I should be on the shipping category index page
          And I should see "Shipping category has been successfully created."

    Scenario: Created shipping categories appear in the list
        Given I created shipping category "Light"
          And I go to the shipping category index page
         Then I should see 3 shipping categories in the list
          And I should see shipping category with name "Light" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the shipping category index page
         When I click "edit" near "Heavy"
         Then I should be editing shipping category "Heavy"

    Scenario: Updating the shipping category
        Given I am editing shipping category "Heavy"
         When I fill in "Name" with "Light"
          And I press "Save changes"
         Then I should be on the shipping category index page
          And I should see "Shipping category has been successfully updated."

    Scenario: Deleting shipping category from list
        Given I am on the shipping category index page
         When I click "delete" near "Heavy"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the shipping category index page
          And I should see "Shipping category has been successfully deleted."

    @javascript
    Scenario: Deleting shipping category from list with js modal
        Given I am on the shipping category index page
         When I click "delete" near "Heavy"
          And I click "delete" from the confirmation modal
         Then I should be on the shipping category index page
          And I should see "Shipping category has been successfully deleted."

    Scenario: Deleted shipping category disappears from the list
        Given I am on the shipping category index page
         When I click "delete" near "Regular"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the shipping category index page
          And I should not see shipping category with name "Regular" in that list

    @javascript
    Scenario: Deleted shipping category disappears from the list with js modal
        Given I am on the shipping category index page
         When I click "delete" near "Regular"
          And I click "delete" from the confirmation modal
         Then I should be on the shipping category index page
          And I should not see shipping category with name "Regular" in that list
