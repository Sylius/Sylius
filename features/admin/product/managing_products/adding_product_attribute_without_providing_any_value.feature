@managing_products
Feature: Adding a product attribute without providing any value
    In order to avoid mistakes while adding a product attribute
    As an Administrator
    I want to be informed when I try to add an empty product attribute

    Background:
        Given the store operates on a single channel in "United States"
        And the locale "French (France)" is enabled
        And the store also operates in "French (France)" locale
        And the store has text product attribute "Color"
        And I am logged in as an administrator

    @ui @mink:chromedriver @no-api
    Scenario: Adding a product attribute without providing any value
        When I want to create a new configurable product
        And I specify its code as "Jeans"
        And I name it "Colored jeans" in "English (United States)"
        And I add the "Color" attribute
        And I try to add it
        Then I should be notified that the "Color" attribute value for "English (United States)" is required
