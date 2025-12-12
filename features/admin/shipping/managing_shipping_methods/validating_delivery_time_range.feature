@managing_shipping_methods
Feature: Validating delivery time range on shipping method form
    In order to avoid invalid delivery time configuration
    As an Administrator
    I want to be prevented from saving a shipping method when maximum delivery time is less than minimum

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store is available in "English (United States)"
        And the store has a zone "United States" with code "US"
        And I am logged in as an administrator

    @ui @mink:chromedriver
    Scenario: Trying to add a shipping method with invalid delivery time range
        When I want to create a new shipping method
        And I specify its code as "DELIVERY_TIME_INVALID"
        And I name it "Delivery Time Invalid" in "English (United States)"
        And I define it for the zone named "United States"
        And I choose "Flat rate per shipment" calculator
        And I specify its amount as 10 for "Web-US" channel
        And I fill in "Minimum delivery time (days)" with "5"
        And I fill in "Maximum delivery time (days)" with "3"
        And I try to add it
        Then I should be notified that Maximum delivery time must be greater than or equal to the minimum.
