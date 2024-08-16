@checkout
Feature: Preventing a potential XSS attack during updating the address in the checkout
    In order to keep my information safe
    As a Visitor
    I want to be protected against the potential XSS attacks

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack during updating the address in the checkout
        When I specify the email as "john.doe@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Doe"
        And I specify the province name manually as '<img """><script>alert("XSS")</script>">' for billing address
        And I complete the addressing step
        And I decide to change my address
        Then I should be able to update the address without unexpected alert
