@viewing_products
Feature: Accessing the simple product show page from product index
    In order to view detailed product information
    As a Administrator
    I want to be able to view product show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over sized" and "Standard" shipping category
        And the store has a tax category "No tax" with a code "nt"
        And the store classifies its products as "Shield" and "Equipment"
        And the store has a product "Iron shield" priced at "$20.00"
        And the product "Iron shield" has orginal price "$25.00"
        And there are 4 units of product "Iron shield" available in the inventory
        And the "Iron shield" product has an image "mugs.jpg" with "main" type
        And product "Iron shield" have the code equals "123456789"
        And it belongs to "No tax" tax category
        And product "Iron shield" has height "10.0", width "15.0", depth "20.0", weight "25.0"
        And the product "Iron shield" has the slug "iron-shield"
        And product "Iron shield" has a main taxon "Equipment"
        And this product belongs to "Shield"
        And this product belongs to "Over sized" shipping category
        And the description of product "Iron shield" should be "Shield created by dwarf"
        And the meta keywords of product "Iron shield" should be "shield"
        And the short description of product "Iron shield" should be "good shield"
        And this product has text attribute "serial number" with value "987654"
        And the store has "Similar" and "dwarf equipment" product association types
        And the store has a "Glass shield" product
        And the product "Iron shield" has an association "Similar" with product "Glass shield"
        And I am logged in as an administrator
        And I browse products

    @ui
    Scenario: Viewing a simple product show page
        When I access "Iron Shield" product page
        Then I should see product show page without variants
        And I should see product name "Iron shield"

    @ui
    Scenario: Viewing pricing block
        When I access "Iron Shield" product page
        Then I should see price "$20.00" for channel "United States"
        And I should see original price "$25.00" for channel "United States"

    @ui
    Scenario: Viewing details block
        When I access "Iron Shield" product page
        Then I should see product's code "123456789"
        And I should see product's channels "United States"
        And I should see current stock of this product 4
        And I should see product's tax category "No tax"

    @ui
    Scenario: Viewing taxonomy block
        When I access "Iron Shield" product page
        Then I should see main taxon is "Equipment"
        And I should see product taxon is "Shield"

    @ui
    Scenario: Viewing shipping block
        When I access "Iron Shield" product page
        Then I should see product's shipping category "Over sized"
        And I should see product's height is 10
        And I should see product's width is 15
        And I should see product's depth is 20
        And I should see product's weight is 25

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
