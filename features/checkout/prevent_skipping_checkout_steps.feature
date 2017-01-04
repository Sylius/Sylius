@checkout
Feature: Prevent skipping checkout steps
    In order to get back to the right checkout step after leaving
    As a Customer
    I want to be able to finish checkout process

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has a product "Paganini T-Shirt" priced at "$10.00"
        And there is a promotion "Holiday promotion"
        And the promotion gives "$29.99" discount to every order with quantity at least 2
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Skipping shipping checkout step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I want to complete checkout
        Then I should be on the checkout shipping step

    @ui
    Scenario: Skipping payment checkout step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Free" shipping method
        And I complete the shipping step
        When I want to complete checkout
        Then I should be on the checkout payment step

    @ui
    Scenario: Skipping addressing checkout step
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I want to complete checkout
        Then I should be on the checkout addressing step

    @ui
    Scenario: Skipping addressing checkout step when order total is zero
        Given I have product "PHP T-Shirt" in the cart
        And I have product "Paganini T-Shirt" in the cart
        And I am at the checkout addressing step
        When I want to complete checkout
        Then I should be on the checkout addressing step

    @ui
    Scenario: Not being able to skip the checkout shipping selection step when order total is zero
        Given I have product "PHP T-Shirt" in the cart
        And I have product "Paganini T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I want to complete checkout
        Then I should be on the checkout shipping step

    @ui
    Scenario: Not being able go to payment checkout step when order total is zero and payments not exists
        Given I have product "PHP T-Shirt" in the cart
        And I have product "Paganini T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have selected "Free" shipping method
        And I complete the shipping step
        When I want to pay for order
        Then I should be on the checkout complete step
