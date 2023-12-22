@managing_product_attributes
Feature: Seeing correct select attribute values in different locale than default one
    In order to see correct attribute values in different locale than default one
    As an Administrator
    I should be able to create attribute with values in different locale than default one

    Background:
        Given the store is available in "French (France)"
        And I am logged in as an administrator

    @ui @javascript @no-api
    Scenario: Seeing correct attribute values in different locale than default one
        When I want to create a new select product attribute
        And I specify its code as "mug_material"
        And I name it "Mug material" in "French (France)"
        And I add value "Banana Skin" in "French (France)"
        And I add it
        Then I should be notified that it has been successfully created
        And I should see the value "Banana Skin"
