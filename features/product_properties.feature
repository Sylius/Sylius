Feature: Product properties
    As a store owner
    I want to be able to configure product properties
    In order to show specific product parameters to customer

    Background:
        Given I am logged in as administrator
          And there are following properties:
            | name               | presentation   |
            | T-Shirt collection | Collection     |
            | T-Shirt fabric     | T-Shirt fabric |

    Scenario: Seeing index of all properties
        Given I am on the dashboard page
         When I follow "Configure properties"
         Then I should be on the property index page
          And I should see 2 properties in the list

    Scenario: Seeing empty index of properties
        Given there are no properties
         When I am on the property index page
         Then I should see "There are no properties configured"

    Scenario: Accessing the property creation form
        Given I am on the dashboard page
         When I follow "Configure properties"
          And I follow "Create property"
         Then I should be on the property creation page

    Scenario: Submitting form without specifying the name
        Given I am on the property creation page
         When I press "Create"
         Then I should still be on the property creation page
          And I should see "Please enter property name"

    Scenario: Submitting form without specifying the presentation
        Given I am on the property creation page
         When I fill in "Internal name" with "Book author"
          And I press "Create"
         Then I should still be on the property creation page
          And I should see "Please enter property presentation"

    Scenario: Creating new property
        Given I am on the property creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I press "Create"
         Then I should still be on the property index page
          And I should see "Property has been successfully created."

    Scenario: Created properties appear in the list
        Given I created property "Food"
         When I go to the property index page
         Then I should see 3 properties in the list
          And I should see property with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the property index page
         When I click "Edit" near "T-Shirt collection"
         Then I should be editing property "T-Shirt collection"

    Scenario: Updating the property
        Given I am editing property "T-Shirt collection"
         When I fill in "Internal name" with "T-Shirt edition"
          And I press "Save changes"
         Then I should still be on the property index page
          And I should see "Property has been successfully updated."

    Scenario: Deleted property disappears from the list
        Given I am on the property index page
         When I click "Delete" near "T-Shirt fabric"
         Then I should still be on the property index page
          And I should see "Property has been successfully deleted."
          And I should not see property with name "T-Shirt fabric" in that list
