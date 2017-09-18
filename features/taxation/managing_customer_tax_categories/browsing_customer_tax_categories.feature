@managing_customer_tax_categories
Feature: Browsing customer tax categories
    In order to see all customer tax categories in the store
    As an Administrator
    I want to be able to browse customer tax categories

    Background:
        Given the store has a customer tax category "Retail"
        And the store has a customer tax category "Wholesale"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Browsing customer tax categories
        When I browse customer tax categories
        Then I should see 2 customer tax categories in the list
        And I should see the customer tax category "Retail" in the list
        And I should also see the customer tax category "Wholesale" in the list
