@managing_customers
Feature: Filtering customers by groups
    In order to quickly find customers belonging to specific groups
    As an Administrator
    I want to be able to filter customers on the list

    Background:
        Given the store has a customer group "Retail"
        And the store has a customer group "Wholesale"
        And the store has customer "f.baggins@example.com"
        And the store has customer "g.bespoke@example.com"
        And this customer belongs to group "Retail"
        And the store has customer "l.abhorsen@example.com"
        And this customer belongs to group "Wholesale"
        And I am logged in as an administrator

    @api @ui @javascript
    Scenario: Filtering customers by a group
        When I want to see all customers in store
        And I filter by group "Retail"
        Then I should see a single customer on the list
        And I should see the customer "g.bespoke@example.com" in the list

    @api @ui @mink:chromedriver
    Scenario: Filtering customers by multiple groups
        When I want to see all customers in store
        And I filter by groups "Retail" and "Wholesale"
        Then I should see 2 customers in the list
        And I should see the customer "l.abhorsen@example.com" in the list
        And I should see the customer "g.bespoke@example.com" in the list
