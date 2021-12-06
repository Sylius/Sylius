@applying_catalog_promotions
Feature: Reapplying catalog promotions after editing their actions
    In order to have proper discounts per catalog promotion action
    As a Store Owner
    I want to have discounts reapplied in product catalog once the action of catalog promotion changes

    Background:
        Given the store operates on a channel identified by "Web-US" code
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And this product has "Python T-Shirt" variant priced at "$10.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui @javascript
    Scenario: Reapplying catalog promotion after adding a new action to it
        Given there is a catalog promotion with "Summer_sale" code and "Summer sale" name
        And it applies on "Python T-Shirt" variant
        When I modify a catalog promotion "Summer sale"
        And I add action that gives "25%" percentage discount
        And I save my changes
        And the visitor view "Python T-Shirt" variant
        Then this product variant price should be "$7.50"
        And this product original price should be "$10.00"

    @api @ui @javascript
    Scenario: Reapplying catalog promotion after editing its action
        When I modify a catalog promotion "Winter sale"
        And I edit its action so that it reduces price by "25%"
        And I save my changes
        And the visitor view "PHP T-Shirt" variant
        Then this product variant price should be "$15.00"
        And this product original price should be "$20.00"

    @api @ui @javascript
    Scenario: Reapplying catalog promotion after removing and adding new action
        When I modify a catalog promotion "Winter sale"
        And I remove its every action
        And I save my changes
        And I add action that gives "10%" percentage discount
        And I save my changes
        And the visitor view "PHP T-Shirt" variant
        Then this product variant price should be "$18.00"
        And this product original price should be "$20.00"

    @api @ui @javascript
    Scenario: Reapplying catalog promotion after adding another action
        When I modify a catalog promotion "Winter sale"
        And I add another action that gives "10%" percentage discount
        And I save my changes
        And the visitor view "PHP T-Shirt" variant
        Then this product variant price should be "$9.00"
        And this product original price should be "$20.00"

    @api @ui @javascript
    Scenario: Restoring original price after removing action from catalog promotion configuration
        When I modify a catalog promotion "Winter sale"
        And I remove its every action
        And I save my changes
        Then the visitor should see that the "PHP T-Shirt" variant is not discounted
