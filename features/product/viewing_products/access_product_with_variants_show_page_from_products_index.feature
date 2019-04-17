@viewing_products
Feature: Accessing the product with wariants show page from product index
    In order to view detailed product information
    As a Administrator
    I want to be able to view product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over sized" and "Standard" shipping category
        And the store classifies its products as "Shield" and "Equipment"
        And the store has "Similar" and "dwarf equipment" product association types
        And the store has a product option "Shield size" with a code "shield_size"
        And the store has a "Iron shield" configurable product
        And product "Iron shield" has option "Shield size" named "XL" with code "shield_size_xl"
        And product "Iron shield" has option "Shield size" named "XS" with code "shield_size_xs"
        And product "Iron shield" has "Iron shield - very big" variant with code "123456789-xl", price "$25.00", current stock 5
        And product "Iron shield" has "Iron shield - very small" variant with code "123456789-xs", price "$15.00", current stock 12
        And the "Iron shield" product has an image "mugs.jpg" with "main" type
        And the product "Iron shield" has the slug "iron-shield"
        And product "Iron shield" has a main taxon "Equipment"
        And this product belongs to "Shield"
        And the description of product "Iron shield" should be "Shield created by dwarf"
        And the meta keywords of product "Iron shield" should be "shield"
        And the short description of product "Iron shield" should be "good shield"
        And this product has text attribute "serial number" with value "987654"
        And the store has a "Glass shield" product
        And the product "Iron shield" has an association "Similar" with product "Glass shield"
        And I am logged in as an administrator
        And I browse products

    @ui
    Scenario: Viewing a configurable product show page
        When I access "Iron Shield" product page
        Then I should see product show page with variants
        And I should see product name "Iron shield"

    @ui
    Scenario: Viewing taxonomy block
        When I access "Iron Shield" product page
        Then I should see main taxon is "Equipment"
        And I should see product taxon is "Shield"

    @ui
    Scenario: Viewing options block
        When I access "Iron Shield" product page
        Then I should see option "Shield size"

    @ui
    Scenario: Viewing variants block
        When I access "Iron Shield" product page
        Then I should see 2 variants
        And I should see "Iron shield" variant with code "123456789-xl", priced "$25.00" and current stock 5
        And I should see "Iron shield" variant with code "123456789-xs", priced "$15.00" and current stock 12

    @ui @javascript
    Scenario: Viewing media block
        When I access "Iron Shield" product page
        Then I should see image

    @ui
    Scenario: Viewing "more details" block
        When I access "Iron Shield" product page
        Then I should see product name is "Iron shield"
        And I should see product slug is "iron-shield"
        And I should see product's description is "Shield created by dwarf"
        And I should see product's meta keywords is "shield"
        And I should see product's short description is "good shield"

    @ui
    Scenario: Viewing attributes block
        When I access "Iron Shield" product page
        Then I should see "serial number" is 987654

    @ui
    Scenario: Viewing Associations block
        When I access "Iron Shield" product page
        Then I should see product association "Similar" with "Glass shield"
