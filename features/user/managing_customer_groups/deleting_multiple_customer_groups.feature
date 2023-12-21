@managing_customer_groups
Feature: Deleting multiple customer groups
    In order to remove test, obsolete or incorrect customer groups in an efficient way
    As an Administrator
    I want to be able to delete multiple customer groups at once

    Background:
        Given the store has customer groups "Retail", "Wholesale" and "General"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple customer groups at once
        When I browse customer groups
        And I check the "Retail" customer group
        And I check also the "Wholesale" customer group
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single customer group in the list
        And I should see the customer group "General" in the list
