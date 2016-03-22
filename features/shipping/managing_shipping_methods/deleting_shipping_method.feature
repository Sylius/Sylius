@shipping
Feature: Deleting a shipping method
    In order to remove not used or invalid shipping methods
    As an Administrator
    I want to be able to delete a shipping method

    Background:
        Given the store operates on a single channel in "France"
        And the store allows shipping with "UPS Ground"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Successfully deleting a shipping method when it's not used
        When I try to delete "UPS Ground" shipping method
        Then it should be successfully removed
