@managing_shipping_methods
Feature: Seeing errors in shipping method charges
    In order to quickly find the errors in the form
    As an Administrator
    I want to see the errors count on the channel tabs

    Background:
        Given the store operates on a channel named "Web" in "USD" currency
        And the store also operates on a channel named "Mobile" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing the number of errors with per shipment charges
        Given the store has "FedEx Carrier" shipping method with "$20.00" fee per shipment for "Web" channel and "$15.00" for "Mobile" channel
        When I want to modify this shipping method
        And I remove the shipping charges of "Mobile" channel
        And I try to save my changes
        Then I should see that the shipping charges for "Mobile" channel has 1 validation error

    @ui @no-api
    Scenario: Seeing the number of errors with per unit charges
        Given the store has "FedEx Carrier" shipping method with "$20.00" fee per unit for "Web" channel and "$15.00" for "Mobile" channel
        When I want to modify this shipping method
        And I remove the shipping charges of "Mobile" channel
        And I try to save my changes
        Then I should see that the shipping charges for "Mobile" channel has 1 validation error
