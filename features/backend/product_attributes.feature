@products
Feature: Product attributes
    In order to show specific product parameters to customer
    As a store owner
    I want to be able to configure product attributes

    Background:
        Given I am logged in as administrator
          And there are following attributes:
            | name               | presentation   |
            | T-Shirt collection | Collection     |
            | T-Shirt fabric     | T-Shirt fabric |

    Scenario: Seeing index of all attributes
        Given I am on the dashboard page
         When I follow "Configure attributes"
         Then I should be on the product attribute index page
          And I should see 2 attributes in the list

    Scenario: Seeing empty index of attributes
        Given there are no product attributes
         When I am on the product attribute index page
         Then I should see "There are no attributes configured"

    Scenario: Accessing the product attribute creation form
        Given I am on the dashboard page
         When I follow "Configure attributes"
          And I follow "Create product attribute"
         Then I should be on the product attribute creation page

    Scenario: Submitting form without specifying the name
        Given I am on the product attribute creation page
         When I press "Create"
         Then I should still be on the product attribute creation page
          And I should see "Please enter attribute name"

    Scenario: Submitting form without specifying the presentation
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I press "Create"
         Then I should still be on the product attribute creation page
          And I should see "Please enter attribute presentation"

    Scenario: Creating new attribute
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I press "Create"
         Then I should still be on the product attribute index page
          And I should see "Attribute has been successfully created."

    Scenario: Created attributes appear in the list
        Given I created attribute "Food"
         When I go to the product attribute index page
         Then I should see 3 attributes in the list
          And I should see attribute with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the product attribute index page
         When I click "edit" near "T-Shirt collection"
         Then I should be editing product attribute "T-Shirt collection"

    Scenario: Updating the attribute
        Given I am editing product attribute "T-Shirt collection"
         When I fill in "Internal name" with "T-Shirt edition"
          And I press "Save changes"
         Then I should still be on the product attribute index page
          And I should see "Attribute has been successfully updated."

    @javascript
    Scenario: Deleted attribute disappears from the list
        Given I am on the product attribute index page
         When I click "delete" near "T-Shirt fabric"
          And I click "delete" from the confirmation modal
         Then I should still be on the product attribute index page
          And I should see "Attribute has been successfully deleted."
          And I should not see attribute with name "T-Shirt fabric" in that list

    Scenario: Creating string attribute by default
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I press "Create"
         Then I should still be on the product attribute index page
          And I should see "Attribute has been successfully created."
          And product attribute with following data should be created:
            | name | Book author |
            | type | text        |

    Scenario Outline: Creating new attribute for type
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I select "<label>" from "Type"
          And I press "Create"
         Then I should still be on the product attribute index page
          And I should see "Attribute has been successfully created."
          And product attribute with following data should be created:
            | name         | Book author |
            | presentation | Author      |
            | type         | <value>     |

        Examples:
          | label    | value    |
          | Checkbox | checkbox |
          | Text     | text     |
          | Choice   | choice   |
          | Number   | number   |

    @javascript
    Scenario: Create new choice attribute with many choices
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I select "Choice" from "Type"
          And I click "Add choice"
          And I fill in "Choice 0" with "J.R.R Tolken"
          And I click "Add choice"
          And I fill in "Choice 1" with "Jaroslaw Grzedowicz"
          And I press "Create"
         Then product attribute with following data should be created:
            | name         | Book author                      |
            | presentation | Author                           |
            | type         | choice                           |
            | choices      | J.R.R Tolken,Jaroslaw Grzedowicz |
          And I should see "Attribute has been successfully created."

    @javascript
    Scenario: Remove choice attribute choice
        Given I am on the product attribute creation page
         When I fill in "Internal name" with "Book author"
          And I fill in "Presentation" with "Author"
          And I select "Choice" from "Type"
          And I click "Add choice"
          And I fill in "Choice 0" with "J.R.R Tolken"
          And I click "Add choice"
          And I fill in "Choice 1" with "Jaroslaw Grzedowicz"
          And I remove attribute choice number 0
          And I press "Create"
         Then product attribute with following data should be created:
            | name         | Book author         |
            | presentation | Author              |
            | type         | choice              |
            | choices      | Jaroslaw Grzedowicz |
          And I should see "Attribute has been successfully created."
