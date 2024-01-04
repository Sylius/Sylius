@viewing_products
Feature: Viewing diagonal variants options
    In order to buy available products
    As a Customer
    I want to see proper options and prices when only diagonal variants are enabled like the following table:

    | Size\Color    | Yellow    | Blue  |
    | S             | X         |       |
    | L             |           | X     |

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Extra Cool T-Shirt" configurable product
        And this product has option "Size" with values "Small" and "Large"
        And this product has option "Color" with values "Yellow" and "Blue"
        And this product has all possible variants
        But the "Small" size / "Blue" color variant of product "Extra Cool T-Shirt" is disabled
        And the "Large" size / "Yellow" color variant of product "Extra Cool T-Shirt" is disabled

    @api @ui
    Scenario: Viewing both values for both options when diagonal variants are available
        When I view product "Extra Cool T-Shirt"
        Then I should be able to select the "Yellow" and "Blue" Color option values
        And I should be able to select the "Small" and "Large" Size option values

    @ui @javascript @no-api
    Scenario: Viewing an "Unavailable" message when selecting an unavailable combination
        When I view product "Extra Cool T-Shirt"
        And I select its color as "Blue"
        And I select its size as "Small"
        Then I should see that the combination is "Unavailable"
        And I should be unable to add it to the cart

    @api @no-ui
    Scenario: Not seeing unavailable variants
        When I view variants of the "Extra Cool T-Shirt" product
        And I filter them by "Blue" option value
        And I filter them by "Small" option value
        Then I should not see any variants
