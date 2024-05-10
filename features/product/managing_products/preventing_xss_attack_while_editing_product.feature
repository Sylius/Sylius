@managing_products
Feature: Preventing a potential XSS attack while selecting similar product
    In order to keep my information safe
    As an Administrator
    I want to be protected against the potential XSS attacks

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product association type "Accessories"
        And the store has "<script>alert('xss')</script>" and "LG headphones" products
        And I am logged in as an administrator

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack while editing product
        When I want to create a new simple product
        Then I should be able to associate as "Accessories" the "LG headphones" product
