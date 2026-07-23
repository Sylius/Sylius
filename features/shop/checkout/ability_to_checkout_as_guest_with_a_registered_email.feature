@checkout
Feature: Checking out as guest with a registered email
    In order to make the checkout process less cumbersome
    As a Visitor
    I want to be able to checkout with my email although I have a registered account

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer account "john@example.com"

    @api @ui
    Scenario: Successfully placing an order
        Given I added product "PHP T-Shirt" to the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Being treated as a guest on the thank you page despite using a registered email
        Given I added product "PHP T-Shirt" to the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
        And I should be able to proceed to the registration
        But I should not be able to access this order's details

    @api @ui
    Scenario: Placing an order using email with mixed case
        Given I added product "PHP T-Shirt" to the cart
        When I complete addressing step with email "JOhn@example.COM" and "United States" based billing address
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
