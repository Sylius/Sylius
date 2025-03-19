@managing_payments
Feature: Seeing payment request's details
    In order to have an overview of one of payment requests
    As an Administrator
    I want to be able to see its details

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "PHP T-Shirt" product
        And the payment request action "authorize" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing payment request's details
        When I view details of the payment request for the "#00000001" order
        Then its method should be "Cash on Delivery"
        And its action should be "Authorize"
        And its state should be "New"
        And its payload should has empty value
        And its response data should has empty value
