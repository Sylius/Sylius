@managing_customer_groups
Feature: Browsing customer groups
    In order to see all customer groups in the store
    As an Administrator
    I want to browse customer groups

    Background:
        Given the store has a customer group "Retail"
        And the store has a customer group "Wholesale"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing customer groups in the store
        When I want to browse customer groups
        Then I should see 2 customer groups in the list
        And I should see the customer group "Retail" in the list
