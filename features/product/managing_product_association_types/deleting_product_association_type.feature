@managing_product_association_types
Feature: Deleting product association types
    In order to remove test, obsolete or incorrect product association types
    As an Administrator
    I want to be able to delete a product association type

    Background:
        Given the store has a product association type "Cross sell"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting a product association type
        When I delete the "Cross sell" product association type
        Then I should be notified that it has been successfully deleted
        And this product association type should no longer exist in the registry
