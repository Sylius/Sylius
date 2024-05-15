@managing_taxons
Feature: Preventing a potential XSS attack while adding a new taxon
    In order to keep my information safe
    As an Administrator
    I want to be protected against the potential XSS attacks

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Category" taxonomy
        And the store has "<script>alert('xss')</script>" taxonomy
        And I am logged in as an administrator

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack while adding new taxon
        When I want to create a new taxon
        Then I should be able to change its parent taxon to "Category"
