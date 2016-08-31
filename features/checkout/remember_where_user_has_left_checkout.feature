@checkout
Feature: Remember where users actually have left checkout
    In order to get back to the right checkout step after leaving
    As a Customer
    I want to be able to finish checkout process

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Leaving shipping checkout step and get back to remaining step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have left the checkout process
        When I want to get back to the remaining step
        And I select "Free" shipping method
        And I complete the shipping step
        And I select "offline" payment method
        And I complete the payment step
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Leaving payment checkout step and get back to remaining step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Free" shipping method
        And I have left the checkout process
        When I want to get back to the remaining step
        And I select "offline" payment method
        And I complete the payment step
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Leaving addressing checkout step and get back to the remaining step
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        And I have left the checkout process
        When I want to get back to the remaining step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        And I select "offline" payment method
        And I complete the payment step
        And I confirm my order
        Then I should see the thank you page
