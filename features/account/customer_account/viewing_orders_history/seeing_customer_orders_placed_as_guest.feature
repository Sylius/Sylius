@customer_account
Feature: Seeing customer's orders placed as guest
    In order to browse orders placed before registration
    As an Customer
    I want to be able to view list of my orders

    Background:
        Given the store operates on a single channel in "United States"
        And on this channel account verification is not required
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the guest customer placed order with "PHP T-Shirt" product for "john@snow.com" and "United States" based billing address with "Free" shipping method and "Offline" payment
        And the another guest customer placed order with "PHP T-Shirt" product for "ned@stark.com" and "United States" based billing address with "Free" shipping method and "Offline" payment

    @ui @api
    Scenario: Not being able to hijack another customer's orders
        Given I registered with previously used "ned@stark.com" email and "lannistersAreDumb" password
        When I log in as "ned@stark.com" with "lannistersAreDumb" password
        And I browse my orders
        Then I should see a single order in the list
