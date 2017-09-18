@managing_customer_tax_categories
Feature: Sorting listed customer tax categories
    In order to change the order by which customer tax categories are displayed
    As an Administrator
    I want to sort customer tax categories

    Background:
        Given the store has a customer tax category "Retail" with a code "retail"
        And the store has a customer tax category "Wholesale" with a code "wholesale"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Customer tax categories are sorted by name and description field in ascending order by default
        When I browse customer tax categories
        Then I should see 2 customer tax categories in the list
        And the first customer tax category in the list should be "Retail"
        And the last customer tax category in the list should be "Wholesale"

    @ui @todo
    Scenario: Changing the order of sorting customer tax categories by their name and description fields
        Given I am browsing customer tax categories
        When I switch the way customer tax categories are sorted by name and description field
        Then I should see 2 customer tax categories in the list
        And the first customer tax category in the list should be "Wholesale"
        And the last customer tax category in the list should be "Retail"

    @ui @todo
    Scenario: Sorting customer tax categories by their codes
        Given I am browsing customer tax categories
        When I start sorting customer tax categories by code
        Then I should see 2 customer tax categories in the list
        And the first customer tax category in the list should be "Retail"
        And the last customer tax category in the list should be "Wholesale"

    @ui @todo
    Scenario: Changing the order of sorting customer tax categories by their codes
        Given I am browsing customer tax categories
        And the customer tax categories are already sorted by code
        When I switch the way customer tax categories are sorted by code
        Then I should see 2 customer tax categories in the list
        And the first customer tax category in the list should be "Wholesale"
        And the last customer tax category in the list should be "Retail"
