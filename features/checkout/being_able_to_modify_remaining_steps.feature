@checkout
Feature: Remember where users actually have left checkout
    In order to get back to the right checkout step after leaving
    As a Customer
    I want to be able to finish checkout process

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store has "Raven Post" shipping method with "$10.00" fee
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Placing an order after moving back from the checkout summary to the addressing step but without any address modification
        Given I had product "PHP T-Shirt" in the cart
        And I was at the checkout summary step
        When I go back to addressing step of the checkout
        But I do not modify address
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Placing an order after moving back from the checkout summary to the shipping method step but without any shipping method modification
        Given I had product "PHP T-Shirt" in the cart
        And I was at the checkout summary step
        When I go back to shipping step of the checkout
        But I do not modify shipping method
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Placing an order after moving back from the checkout summary to the payment method step but without any payment method modification
        Given I had product "PHP T-Shirt" in the cart
        And I was at the checkout summary step
        When I go back to payment step of the checkout
        But I do not modify payment method
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Addressing my order after selecting payment method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to addressing step of the checkout
        And I specify the shipping address as "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Addressing my order after selecting shipping method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I select "Free" shipping method
        And I complete the shipping step
        When I go back to addressing step of the checkout
        And I specify the shipping address as "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Selecting shipping method after selecting payment method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to shipping step of the checkout
        And I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should be on the checkout payment step
