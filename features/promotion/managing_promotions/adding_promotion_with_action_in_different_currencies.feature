@managing_promotions
Feature: Adding a new promotion with action configured in different currencies
    In order to give possibility to pay specifically less price for some goods
    As an Administrator
    I want to add a new promotion with action configured in different currencies to the registry

    Background:
        Given the store has currency "USD"
        And the store has currency "GBP" with exchange rate 0.5
        And the store operates on a channel named "Web-US"
        And that channel allows to shop using "USD" and "GBP" currencies
        And that channel uses the "USD" currency by default
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new promotion with fixed discount
        When I want to create a new promotion
        And I specify its code as "20_for_all_products"
        And I name it "Fixed discount for all products!"
        And I add the "Order fixed discount" action
        And it is configured with base amount of "$20.00"
        And it is also configured with "GBP" amount of "£16.00"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Fixed discount for all products!" promotion should appear in the registry
