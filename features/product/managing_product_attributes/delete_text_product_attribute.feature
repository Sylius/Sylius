@managing_product_attributes
Feature: Deleting a text product attribute
    In order to remove test, obsolete or incorrect text product attribute
    As an Administrator
    I want to be able to delete a text product attribute

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Deleting a text product attribute from the registry
        Given the store has a text product attribute "T-shirt cotton brand"
        When I delete this product attribute
        Then I should be notified that it has been successfully deleted
        And this product attribute should no longer exist in the registry
