@managing_products
Feature: Adding a new product with a percent attribute
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add a new product with a percent attribute to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a percent product attribute "Awesomeness rating"
        And the store has a non-translatable percent product attribute "Accuracy"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a percent attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its "Awesomeness rating" attribute to 80 in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And attribute "Awesomeness rating" of product "44 Magnum" should be 80

    @ui @mink:chromedriver @api
    Scenario: Adding a non-translatable percent attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set its non-translatable "Accuracy" attribute to 95
        And I add it
        Then I should be notified that it has been successfully created
        And the product "44 Magnum" should appear in the store
        And non-translatable attribute "Accuracy" of product "44 Magnum" should be 95

    @api @no-ui
    Scenario: Trying to add an invalid percent attribute to product
        When I want to create a new configurable product
        And I specify its code as "44_MAGNUM"
        And I name it "44 Magnum" in "English (United States)"
        And I set the invalid string value of the non-translatable "Accuracy" attribute to "ninety"
        And I try to add it
        Then I should be notified that the value of the "Accuracy" attribute has invalid type
        And product with code "44_MAGNUM" should not be added
