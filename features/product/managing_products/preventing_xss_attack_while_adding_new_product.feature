@managing_products
Feature: Preventing a potential XSS attack while adding a new product
    In order to keep my information safe
    As an Administrator
    I want to be protected against the potential XSS attacks

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "<script>alert('xss')</script>" taxonomy
        And the store has "No XSS" taxonomy
        And I am logged in as an administrator

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack while adding new product
        When I want to create a new simple product
        Then I should be able to name it "No XSS" in "English (United States)"

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack while choosing main taxon for a new product
        When I want to create a new simple product
        Then I should be able to choose main taxon "No XSS"
