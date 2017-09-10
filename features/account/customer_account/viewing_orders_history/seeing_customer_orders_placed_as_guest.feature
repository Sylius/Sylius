@customer_account
Feature: Seeing customer's orders placed as guest
    In order to browse orders placed before registration
    As an Customer
    I want to be able to view list of my orders

    Background:
        Given the store operates on a single channel in "United States"
        And on this channel account verification is not required
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And the guest customer placed order with "PHP T-Shirt" product for "john@snow.com" and "United States" based shipping address with "Free" shipping method and "Offline" payment

    @ui
    Scenario: Not being able to hijack another customer's orders
        Given I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john@snow.com" and "United States" based shipping address
        And I decide to change my address
        And I specify the email as "ned@stark.com"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        And I select "Offline" payment method
        And I complete the payment step
        And I confirm my order
        And I register with previously used "ned@stark.com" email and "lannistersAreDumb" password
        And I browse my orders
        Then I should see a single order in the list
