@products
Feature: Product attributes
    In order to show specific product parameters to customer
    As a store owner
    I want to be able to configure product attributes

    Background:
        Given I am logged in as administrator
        And there is default currency configured
        And there are following attributes:
            | name               | presentation   |
            | T-Shirt collection | Collection     |
            | T-Shirt fabric     | T-Shirt fabric |

    Scenario: Seeing index of all attributes
        When I go to the product attribute index page
        And I should see 2 attributes in the list

    Scenario: Seeing empty index of attributes
        Given there are no product attributes
        When I am on the product attribute index page
        Then I should see "There are no attributes configured"

    Scenario: Accessing the product attribute creation form
        Given I am on the product attribute index page
        When I follow "Create product attribute"
        Then I should be on the product attribute creation page

    Scenario: Submitting form without required values
        Given I am on the product attribute creation page
        When I press "Save"
        Then I should still be on the product attribute creation page
        And I should see "Please enter attribute name"
        And I should see "Please enter attribute presentation"

    Scenario: Creating new attribute
        Given I am on the product attribute creation page
        When I fill in "Internal name" with "Book author"
        And I fill in "Presentation" with "Author"
        And I press "Save"
        Then I should still be on the product attribute index page
        And I should see "Attribute has been successfully created."

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

    Scenario: Deleted attribute disappears from the list
        Given I am on the product attribute index page
        When I click "delete" near "T-Shirt fabric"
        Then I should still be on the product attribute index page
        And I should see "Attribute has been successfully deleted."
        And I should not see attribute with name "T-Shirt fabric" in that list
