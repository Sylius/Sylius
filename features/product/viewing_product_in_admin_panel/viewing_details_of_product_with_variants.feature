@viewing_products
Feature: Viewing details of a product with variants
    In order to view detailed product information
    As an Administrator
    I want to be able to view product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over sized" and "Standard" shipping category
        And the store has "Similar" and "dwarf equipment" product association types
        And the store has a "Iron shield" configurable product
        And the store has a product option "Shield size" with a code "shield_size"
        And the product "Iron shield" has option "Shield size" named "XL" with code "shield_size_xl"
        And the product "Iron shield" has option "Shield size" named "XS" with code "shield_size_xs"
        And the product "Iron shield" has "Iron shield - very big" variant with code "123456789-xl", price "$25.00", current stock "5"
        And the product "Iron shield" has "Iron shield - very small" variant with code "123456789-xs", price "$15.00", current stock "12"
        And I am logged in as an administrator
        And I am browsing products

    @ui
    Scenario: Viewing a configurable product show page
        When I access "Iron Shield" product page
        Then I should see product show page with variants
        And I should see product name "Iron shield"

    @ui
    Scenario: Viewing taxonomy block
        Given the store classifies its products as "Shield" and "Equipment"
        And the product "Iron shield" has a main taxon "Equipment"
        And the product "Iron shield" belongs to taxon "Shield"
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
        Given the "Iron shield" product has an image "mugs.jpg" with "main" type
        When I access "Iron Shield" product page
        Then I should see an image related to this product

    @ui
    Scenario: Viewing "more details" block
        Given the product "Iron shield" has the slug "iron-shield"
        And the description of product "Iron shield" is "Shield created by dwarf"
        And the meta keywords of product "Iron shield" is "shield"
        And the short description of product "Iron shield" is "good shield"
        When I access "Iron Shield" product page
        Then I should see product name is "Iron shield"
        And I should see product slug is "iron-shield"
        And I should see product's description is "Shield created by dwarf"
        And I should see product's meta keywords is "shield"
        And I should see product's short description is "good shield"

    @ui
    Scenario: Viewing associations block
        Given the store has a "Glass shield" product
        And the product "Iron shield" has an association "Similar" with product "Glass shield"
        When I access "Iron Shield" product page
        Then I should see product association "Similar" with "Glass shield"
