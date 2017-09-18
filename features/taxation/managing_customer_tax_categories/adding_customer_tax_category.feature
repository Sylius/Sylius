@managing_customer_tax_categories
Feature: Adding a new customer tax category
    In order to apply different taxes for items ordered by different customer groups
    As an Administrator
    I want to add a new customer tax category to the registry

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new customer tax category
        When I want to create a new customer tax category
        And I specify its code as "retail"
        And I name it "Retail"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer tax category "Retail" should appear in the registry

    @ui
    Scenario: Adding a new customer tax category with a description
        When I want to create a new customer tax category
        And I specify its code as "retail"
        And I name it "Retail"
        And I describe it as "Retail customers."
        And I add it
        Then I should be notified that it has been successfully created
        And the customer tax category "Retail" should appear in the registry
        And the customer tax category "Retail" description should be "Retail customers."
