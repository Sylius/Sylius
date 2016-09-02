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

    @todo
    Scenario: Placing an order after navigating to to the past steps of a checkout
        Given I’m at the checkout summary step
        When I go back to addressing step of the checkout
        But I do not modify address
        And I return to the checkout summary step
        And I confirm my order
        Then I should be notified that the order has been successfully placed

    @todo
    Scenario: Addressing my order after selecting payment method
        Given I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to the addressing step of the checkout
        And I change the shipping address to „XX"
        And I complete addressing step
        Then I should be redirected to the next step of the checkout
        And I should be on the checkout shipping step
