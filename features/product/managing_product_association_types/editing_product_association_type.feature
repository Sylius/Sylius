@managing_product_association_types
Feature: Editing a product association type
    In order to change information about a product association type
    As an Administrator
    I want to be able to edit the product association type

    Background:
        Given the store is available in "English (United States)"
        And the store has a product association type "Cross sell"
        And I am logged in as an administrator

    @ui
    Scenario: Changing a name of an existing product association type
        When I want to modify the "Cross sell" product association type
        And I rename it to "Up sell" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product association type name should be "Up sell"

    @ui
    Scenario: Seeing a disabled code field while editing a product association type
        When I want to modify the "Cross sell" product association type
        Then the code field should be disabled
