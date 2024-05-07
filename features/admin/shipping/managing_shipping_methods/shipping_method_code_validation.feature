@managing_shipping_methods
Feature: Shipping method code validation
    In order to avoid making mistakes when managing a shipping method
    As an Administrator
    I want to be prevented from adding it with invalid code

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new shipping method with special symbols in the code
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        And I specify its code as "#π/"
        And I try to add it
        Then I should be notified that code needs to contain only specific symbols
        And shipping method with name "FedEx Carrier" should not be added

    @ui @api
    Scenario: Trying to add a new shipping method with spaces in the code
        When I want to create a new shipping method
        And I name it "FedEx Carrier" in "English (United States)"
        And I specify its code as "PEC  -PEC"
        And I try to add it
        Then I should be notified that code needs to contain only specific symbols
        And shipping method with name "FedEx Carrier" should not be added
