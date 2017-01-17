@managing_customer_groups
Feature: Deleting customer groups
    In order to remove test, obsolete or incorrect customer groups
    As an Administrator
    I want to be able to delete a customer group

    Background:
        Given the store has a customer group "Retail"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting a customer group
        When I delete the "Retail" customer group
        Then I should be notified that it has been successfully deleted
        And this customer group should no longer exist in the registry
