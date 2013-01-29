Feature: Tax categories
    As a store owner
    I want to be able to manage tax categories
    In order to apply different tax rates to my products

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
         Then I should be on the page of tax category "Taxable goods"
          And I should see "Category has been successfully created."

    Scenario: Created tax categories appear in the list
        Given I created tax category "Food"
         When I go to the tax category index page
         Then I should see 3 tax categories in the list
          And I should see tax category with name "Food" in that list

    Scenario: Accessing the tax category editing form
        Given I am on the page of tax category "Clothing"
         When I follow "edit"
         Then I should be editing tax category "Clothing"

    Scenario: Accessing the editing form from the list
        Given I am on the tax category index page
         When I click "edit" near "Clothing"
         Then I should be editing tax category "Clothing"

    Scenario: Updating the tax category
        Given I am editing tax category "Clothing"
         When I fill in "Name" with "Clothing & Accessories"
          And I press "Save changes"
         Then I should be on the page of tax category "Clothing & Accessories"
          And I should see "Category has been successfully updated."

    Scenario: Deleting tax category
        Given I am on the page of tax category "Clothing"
         When I follow "delete"
         Then I should be on the tax category index page
          And I should see "Category has been successfully deleted."

    Scenario: Deleted tax category disappears from the list
        Given I am on the page of tax category "Clothing"
         When I follow "delete"
         Then I should be on the tax category index page
          And I should not see tax category with name "Clothing" in that list

    Scenario: Accessing the tax category details page from list
        Given I am on the tax category index page
         When I click "details" near "Clothing"
         Then I should be on the page of tax category "Clothing"
