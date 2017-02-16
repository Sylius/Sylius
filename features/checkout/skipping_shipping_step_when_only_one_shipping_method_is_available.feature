@checkout
Feature: Skipping shipping step when only one shipping method is available
    In order to not select shipping method if its unnecessary
    As a Customer
    I want to be redirected directly to payment selection

    Background:
        Given the store operates on a single channel in "United States"
        And on this channel shipping step is skipped if only a single shipping method is available
        And the store has a product "Guards! Guards!" priced at "$20.00"
        And the store allows paying with "Paypal Express Checkout"
        And I am a logged in customer

    @ui
    Scenario: Seeing checkout payment page after addressing if only one shipping method is available
        Given the store has "DHL" shipping method with "$5.00" fee
        And I have product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Paypal Express Checkout" payment method
        And I complete the payment step
        Then I should be on the checkout complete step
        And my order's shipping method should be "DHL"

    @ui
    Scenario: Seeing checkout payment page after addressing if only one shipping method is available for current channel
        Given the store has "DHL" shipping method with "$5.00" fee
        And the store has "FedEx" shipping method with "$15.00" fee not assigned to any channel
        And I have product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Paypal Express Checkout" payment method
        And I complete the payment step
        Then I should be on the checkout complete step
        And my order's shipping method should be "DHL"

    @ui
    Scenario: Seeing checkout payment page after addressing if only one shipping method is enabled for current channel
        Given the store has "DHL" shipping method with "$5.00" fee
        And the store has disabled "FedEx" shipping method with "$15.00" fee
        And I have product "Guards! Guards!" in the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Paypal Express Checkout" payment method
        And I complete the payment step
        Then I should be on the checkout complete step
        And my order's shipping method should be "DHL"
