@checkout_with_vat
Feature: Order addressing validation with VAT field
    In order to avoid making mistakes when addressing an order
    As an Customer
    I want to be prevented from adding a false VAT number

    Background:
        Given the store operates on a single channel in "United States"
        And its based in the "United States" country and allow VAT numbers for the "US" zone
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store operates in "France"
        And I am a logged in customer

    @ui
    Scenario: Address an order with a country different from the VAT country number
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Eddard Stark"
        And I specify the shipping vat number as "FR01234567891"
        And I specify the billing vat number as "FR01234567891"
        And I try to complete the addressing step
        Then I should be notified that the vat number in shipping details is not corresponding with the selected country
        And I should be notified that the vat number in billing details is not corresponding with the selected country

    @ui
    Scenario: Address an order with a country different from the VAT country number
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Eddard Stark"
        And I specify the shipping vat number as "FR01234567891"
        And I specify the billing vat number as "FR01234567891"
        And I try to complete the addressing step
        And I should be notified that the vat number in shipping details is not valid
        And I should be notified that the vat number in billing details is not valid