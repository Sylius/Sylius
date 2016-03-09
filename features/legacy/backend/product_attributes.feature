@legacy @product
Feature: Product attributes
    In order to show specific product parameters to customer
    As a store owner
    I want to be able to configure product attributes

    Background:
        Given store has default configuration
        And there are following attributes:
            | name        | type     | code        |
            | Book author | text     | book_author |
            | Hardcover   | checkbox | hardcover   |
        And I am logged in as administrator

    Scenario: Seeing attributes list
        Given I am on the dashboard page
        When I follow "Configure attributes"
        Then I should be on the product attribute index page
        And I should see 2 attributes in the list

    Scenario: Seeing empty list of attributes
        Given there are no product attributes
        When I am on the product attribute index page
        Then I should see "There are no attributes configured"

    Scenario: Submitting form without specifying the name
        Given I am on the product attribute creation page
        When I press "Create"
        Then I should still be on the product attribute creation page
        And I should see "Please enter attribute name"
        And I should see "Please enter attribute code"

    @javascript
    Scenario: Accessing attribute creation form
        Given I am on the product attribute index page
        When I click "Create"
        And I choose "checkbox" attribute type
        Then I should be on the product attribute creation page for "checkbox" type
        And I should see select "Type" with "Checkbox" option selected
        And I should not be able to edit "Type" select

    Scenario: Accessing attribute creation form without type specified
        Given I am on the product attribute creation page
        Then I should see select "Type" with "Text" option selected
        And I should not be able to edit "Type" select

    Scenario: Creating new attribute
        Given I am on the product attribute creation page with type "text"
        When I fill in "Name" with "ISBN number"
        And I fill in "Code" with "isbn"
        And I press "Create"
        Then I should be on the product attribute index page
        And I should see "Attribute has been successfully created"
        And I should see 3 attributes in the list
        And I should see attribute with name "ISBN number" in that list

    Scenario: Creating new attribute with configuration
        Given I am on the product attribute creation page with type "date"
        When I fill in the following:
            | Name   | Released at  |
            | Code   | release_date |
            | Format | d/m/Y        |
        And I press "Create"
        Then I should be on the product attribute index page
        And I should see "Attribute has been successfully created"
        And I should see 3 attributes in the list
        And I should see attribute with name "Released at" in that list

    Scenario: Creating new attribute with validation options
        Given I am on the product attribute creation page with type "text"
        When I fill in the following:
            | Name       | Music genre |
            | Code       | music_genre |
            | Min length | 2           |
            | Max length | 255         |
        And I press "Create"
        Then I should be on the product attribute index page
        And I should see "Attribute has been successfully created"
        And I should see 3 attributes in the list
        And I should see attribute with name "Music genre" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the product attribute index page
        When I click "Edit" near "Book author"
        Then I should be editing product attribute "Book author"

    Scenario: Updating the attribute
        Given I am editing product attribute "Book author"
        When I fill in "Name" with "Author"
        And I press "Save changes"
        Then I should still be on the product attribute index page
        And I should see "Attribute has been successfully updated"

    @javascript
    Scenario: Deleting attribute from the list
        Given I am on the product attribute index page
        When I click "Delete" near "Book author"
        And I click "Delete" from the confirmation modal
        Then I should still be on the product attribute index page
        And I should see "Attribute has been successfully deleted"
        And I should not see attribute with name "Book author" in that list
