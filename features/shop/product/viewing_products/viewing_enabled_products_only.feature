@viewing_products
Feature: Viewing enabled products only
    In order to see only available products
    As a Customer
    I want to see only enabled product variants

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Super Cool T-Shirt" product
        And the store has a "PHP T-Shirt" product
        And the store has a "Shiny T-Shirt" product
        And the "PHP T-Shirt" product is disabled

    @api
    Scenario: Seeing only enabled products
        When I browse products
        Then I should see only 2 products
        And I should not see the product with name "PHP T-Shirt"
