@checkout
Feature: Changing checkout steps
    In order to have possibility to change remaining steps
    As a Customer
    I want to be able to modify these steps

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store has "Raven Post" shipping method with "$10.00" fee
        And the store allows paying offline
        And the store allows paying "PayPal Express Checkout"
        And I am a logged in customer

    @ui
    Scenario: Changing address of my order
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Addressing my order after selecting payment method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Addressing my order after selecting shipping method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded selecting "Free" shipping method
        When I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui
    Scenario: Changing shipping method of my order
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded selecting "Free" shipping method
        When I go back to shipping step of the checkout
        And I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should be on the checkout payment step

    @ui
    Scenario: Selecting shipping method after selecting payment method
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to shipping step of the checkout
        And I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should be on the checkout payment step

    @ui
    Scenario: Selecting payment method after complete checkout
        Given I had product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I go back to payment step of the checkout
        And I select "PayPal Express Checkout" payment method
        And I complete the payment step
        Then I should be on the checkout summary step
