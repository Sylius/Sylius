@checkout
Feature: Changing checkout steps
    In order to have possibility to change remaining steps
    As a Customer
    I want to be able to modify these steps

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store has "Raven Post" shipping method with "$10.00" fee
        And the store allows paying Offline
        And the store allows paying "Bank transfer"
        And I am a logged in customer

    @no-api @ui @javascript
    Scenario: Changing address of my order
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @no-api @ui @javascript
    Scenario: Addressing my order after selecting payment method
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @no-api @ui @javascript
    Scenario: Addressing my order after selecting shipping method
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded selecting "Free" shipping method
        And I go back to addressing step of the checkout
        And I change the shipping address to "Ankh Morpork", "Fire Alley", "90350", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @no-api @ui @javascript
    Scenario: Changing shipping method of my order
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded selecting "Free" shipping method
        And I go back to shipping step of the checkout
        And I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should be on the checkout payment step

    @no-api @ui @javascript
    Scenario: Selecting shipping method after selecting payment method
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I go back to shipping step of the checkout
        And I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should be on the checkout payment step

    @no-api @ui @javascript
    Scenario: Selecting payment method after complete checkout
        When I add product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I go back to payment step of the checkout
        And I select "Bank transfer" payment method
        And I complete the payment step
        Then I should be on the checkout summary step
