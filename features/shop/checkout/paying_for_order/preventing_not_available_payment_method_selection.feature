@checkout
Feature: Preventing not available payment method selection
    In order to pay for my order properly
    As a Customer
    I want to be prevented from selecting not available payment methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store allows paying with "Offline"
        And the store allows paying with "Bank transfer"
        And the store ships everywhere for Free
        And I am a logged in customer

    @api @ui @mink:chromedriver
    Scenario: Not being able to select disabled payment method
        Given the payment method "Offline" is disabled
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        Then I should not be able to select "Offline" payment method

    @api @ui @mink:chromedriver
    Scenario: Not being able to select payment method not available for order channel
        Given the store has "Cash on delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        Then I should not be able to select "Cash on delivery" payment method
        And I should be able to select "Offline" payment method

    @api @no-ui
    Scenario: Preventing customer from selecting nonexistent payment method
        When I add product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        And I try to select "Free" payment method
        Then I should be informed that payment method with code "Free" does not exist
