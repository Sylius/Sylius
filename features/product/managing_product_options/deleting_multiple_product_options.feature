@managing_product_options
Feature: Deleting multiple product options
    In order to remove test, obsolete or incorrect product options in an efficient way
    As an Administrator
    I want to be able to delete multiple product options at once

    Background:
        Given the store has a product option "T-Shirt size"
        And the store has also a product option "T-Shirt color"
        And the store has also a product option "T-Shirt brand"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple product options at once
        When I browse product options
        And I check the "T-Shirt size" product option
        And I check also the "T-Shirt color" product option
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product option in the list
        And I should see the product option "T-Shirt brand" in the list
