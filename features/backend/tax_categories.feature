@taxation
Feature: Tax categories
    In order to apply different tax rates to my products
    As a store owner
    I want to be able to manage tax categories

    Background:
        Given I am logged in as administrator
          And there are following tax categories:
            | name        |
            | Clothing    |
            | Electronics |

    Scenario: Seeing index of all tax categories
        Given I am on the dashboard page
         When I follow "Taxation categories"
         Then I should be on the tax category index page
          And I should see 2 tax categories in the list

    Scenario: Seeing empty index of tax categories
        Given there are no tax categories
         When I am on the tax category index page
         Then I should see "There are no tax categories configured"

    Scenario: Accessing the tax category creation form
        Given I am on the dashboard page
         When I follow "Taxation categories"
          And I follow "Create tax category"
         Then I should be on the tax category creation page

    Scenario: Submitting form without specifying the name
        Given I am on the tax category creation page
         When I press "Create"
         Then I should still be on the tax category creation page
          And I should see "Please enter tax category name"

    Scenario: Creating new tax category
        Given I am on the tax category creation page
         When I fill in "Name" with "Taxable goods"
          And I press "Create"
         Then I should be on the tax category index page
          And I should see "Tax category has been successfully created."

    Scenario: Created tax categories appear in the list
        Given I created tax category "Food"
         When I go to the tax category index page
         Then I should see 3 tax categories in the list
          And I should see tax category with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the tax category index page
         When I click "edit" near "Clothing"
         Then I should be editing tax category "Clothing"

    Scenario: Updating the tax category
        Given I am editing tax category "Clothing"
         When I fill in "Name" with "Clothing & Accessories"
          And I press "Save changes"
         Then I should be on the tax category index page
          And I should see "Tax category has been successfully updated."

    Scenario: Deleting tax category
        Given I am on the tax category index page
         When I click "delete" near "Clothing"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the tax category index page
          And I should see "Tax category has been successfully deleted."

    @javascript
    Scenario: Deleting tax category with js modal
        Given I am on the tax category index page
         When I click "delete" near "Clothing"
          And I click "delete" from the confirmation modal
         Then I should be on the tax category index page
          And I should see "Tax category has been successfully deleted."

    Scenario: Deleted tax category disappears from the list
        Given I am on the tax category index page
         When I click "delete" near "Clothing"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the tax category index page
          And I should not see tax category with name "Clothing" in that list

    @javascript
    Scenario: Deleted tax category disappears from the list with js modal
        Given I am on the tax category index page
         When I click "delete" near "Clothing"
          And I click "delete" from the confirmation modal
         Then I should be on the tax category index page
          And I should not see tax category with name "Clothing" in that list
