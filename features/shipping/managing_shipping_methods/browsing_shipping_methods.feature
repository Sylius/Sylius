@managing_shipping_methods
Feature: Browsing shipping methods
    In order to have a overview of all defined shipping methods
    As an Administrator
    I want to be able to browse list of them

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS Carrier" and "FedEx Carrier"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing defined shipping methods
        When I want to browse shipping methods
        Then I should see 2 shipping methods in the list
        And the shipping method "FedEx Carrier" should be in the registry
