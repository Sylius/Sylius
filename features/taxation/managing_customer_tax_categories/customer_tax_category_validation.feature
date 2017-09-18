@managing_customer_tax_categories
Feature: Customer tax category validation
    In order to avoid making mistakes when managing a customer tax category
    As an Administrator
    I want to be prevented from adding it without specifying its code or name

    Background:
        Given I am logged in as an administrator

    @ui @todo
    Scenario: Trying to add a new customer tax category without specifying its code
        When I want to create a new customer tax category
        And I name it "Retail"
        But I do not specify its code
        And I try to add it
        Then I should be notified that the code is required
        And the customer tax category with a name "Retail" should not be added

    @ui @todo
    Scenario: Trying to add a new customer tax category without specifying its name
        When I want to create a new customer tax category
        And I specify its code as "retail"
        But I do not name it
        And I try to add it
        Then I should be notified that the name is required
        And the customer tax category with a code "retail" should not be added

    @ui @todo
    Scenario: Trying to remove a name from an existing customer tax category
        Given the store has a customer tax category "Retail"
        When I want to modify this customer tax category
        And I remove its name
        And I try to save my changes
        Then I should be notified that the name is required
        And this customer tax category should still be named "Retail"
