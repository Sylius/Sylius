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

    @ui
    Scenario: Viewing both values for both options when diagonal variants are available
        When I view product "Extra Cool T-Shirt"
        Then I should be able to select the "Yellow" and "Blue" color option values
        And I should be able to select the "Small" and "Large" size option values

    @ui @javascript
    Scenario: Viewing an "Unavailable" message when selecting an unavailable combination
        When I view product "Extra Cool T-Shirt"
        And I set its color to "Blue"
        And I set its size to "Small"
        Then I should see that the combination is "Unavailable"
        And I should be unable to add it to the cart
