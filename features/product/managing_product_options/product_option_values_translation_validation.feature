@managing_product_options
Feature: Product option values translation validation
    In order to add product option value translation in correct locale
    As an Administrator
    I want to be prevented from adding translation in unexisting locale

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And this product option has the "S" option value with code "OV1"
        And this product option has also the "M" option value with code "OV2"
        And I am logged in as an administrator

    @no-ui @api
    Scenario: Trying to add product option value translation in unexisting locale
        When I want to modify the "T-Shirt size" product option
        And I add the "X" option value identified by "OV3" in "French (France)"
        And I save my changes
        Then I should be notified that the locale is not available
