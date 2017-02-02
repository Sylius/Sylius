@checkout
Feature: Receiving confirmation email after finalizing checkout
    In order to receive proof that my order has been confirmed
    As a Visitor
    I want to receive the order confirmation email

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sig Sauer P226" priced at "$499.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui @email
    Scenario: Order confirmation gets sent after finalizing checkout
        Given I have product "Sig Sauer P226" in the cart
        And I have completed addressing step with email "john@example.com" and "United States" based shipping address
        And I have proceeded order with "Free" shipping method and "Offline" payment
        When I confirm my order
        Then an email with the summary of order placed by "john@example.com" should be sent to him
