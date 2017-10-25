@managing_product_association_types
Feature: Deleting multiple product association types
    In order to remove test, obsolete or incorrect product association types in an efficient way
    As an Administrator
    I want to be able to delete multiple product association types at once

    Background:
        Given the store has a product association type "Cross sell"
        And the store has also a product association type "Up sell"
        And the store has also a product association type "Accessories"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple product association types at once
        When I browse product association types
        And I check the "Cross sell" product association type
        And I check also the "Up sell" product association type
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product association type in the list
        And I should see the product association type "Accessories" in the list
