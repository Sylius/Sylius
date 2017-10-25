@managing_product_attributes
Feature: Deleting multiple product attributes
    In order to remove test, obsolete or incorrect product attributes
    As an Administrator
    I want to be able to delete multiple product attributes

    Background:
        Given the store has a text product attribute "Publisher"
        And the store has a textarea product attribute "Description"
        And the store has a integer product attribute "Pages"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple product attributes
        When I browse product attributes
        And I check the "Publisher" product attribute
        And I check also the "Description" product attribute
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product attribute in the list
        And I should see the product attribute "Pages" in the list
