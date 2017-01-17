@managing_promotions
Feature: Adding a new promotion with rule configured in different channels
    In order to give possibility to pay less for some goods based on specific configuration
    As an Administrator
    I want to add a new promotion with rule configured in different channels to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And the store operates on a channel named "Web-GB" in "GBP" currency
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new promotion with total price of items from taxon rule
        Given I want to create a new promotion
        When I specify its code as "100_IN_EVERY_CURRENCY"
        And I name it "100 in every currency"
        And I add the "Item total" rule configured with €100 amount for "United States" channel and £100 amount for "Web-GB" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "100 in every currency" promotion should appear in the registry
