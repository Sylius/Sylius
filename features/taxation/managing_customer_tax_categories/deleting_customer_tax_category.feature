@managing_customer_tax_categories
Feature: Deleting a customer tax category
    In order to remove test, obsolete or incorrect customer tax categories
    As an Administrator
    I want to be able to delete a customer tax category

    Background:
        Given the store has a customer tax category "Retail"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting a customer tax category
        When I delete a customer tax category "Retail"
        Then I should be notified that it has been successfully deleted
        And this customer tax category should no longer exist in the registry
