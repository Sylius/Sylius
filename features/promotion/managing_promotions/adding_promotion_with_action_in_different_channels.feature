@managing_promotions
Feature: Adding a new promotion with action configured in different channels
    In order to give possibility to pay specifically less price for some goods
    As an Administrator
    I want to add a new promotion with action configured in different channels to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And the store also operates on another channel named "Web-GB" in "GBP" currency
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new promotion with item fixed discount
        When I want to create a new promotion
        And I specify its code as "20_for_all_products"
        And I name it "Item fixed discount for all products!"
        And I add the "Item fixed discount" action configured with amount of "$10" for "United States" channel
        And it is also configured with amount of "£16.00" for "Web-GB" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "Fixed discount for all products!" promotion should appear in the registry
