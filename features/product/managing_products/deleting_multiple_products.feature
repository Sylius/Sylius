@managing_products
Feature: Deleting multiple products
    In order to remove test, obsolete or incorrect products in an efficient way
    As an Administrator
    I want to be able to delete multiple products at once from the product catalog

    Background:
        Given the store has "Audi RS5 model", "Audi RS6 model" and "Audi RS7 model" products
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple products at once
        When I browse products
        And I check the "Audi RS5 model" product
        And I check also the "Audi RS6 model" product
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product in the list
        And I should see the product "Audi RS7 model" in the list
