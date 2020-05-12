[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]
[![SymfonyInsight][ico-symfony-insight]][link-symfony-insight]

# Sylius Plugin for VIES Client

This plugin is adding :

- two new attributes to the `Channel` entity allowing to know from
  which country your shop is from and what's the European zone to target
- VAT Number field to the `Address` entity
- validation over VIES API on this field
- validation of the address country (vat number country should equal to the address country)
- the basic European rule allowing the seller not to charge
  VAT to foreign European customers who have provided a valid VAT number :
    - (Shop country === customer country vat number) then do nothing
    - (Shop country !== customer country vat number) then remove taxes

## Screenshots

### Shop checkout

![Shop checkout address step form](docs/assets/shop-checkout-address-form.png "Shop checkout address step form")

![Shop checkout summary page](docs/assets/shop-checkout-summary.png "Shop checkout summary page")

### Shop account

![Shop account address book](docs/assets/shop-account-address-book.png "Shop account address book")

![Shop account order details](docs/assets/shop-account-order-details.png "Shop account order details")

## Installation

```bash
composer require prometee/sylius-vies-client-plugin
```
## Configuration

Enable this plugin :

```php
<?php

# config/bundles.php

return [
    // ...
    Prometee\SyliusVIESClientPlugin\PrometeeSyliusVIESClientPlugin::class => ['all' => true],
    // ...
];
```

Add default config if you want to get access to the default fixtures this plugin need.

```yaml
# config/packages/prometee_sylius_vies_client.yaml

imports:
    - { resource: "@PrometeeSyliusVIESClientPlugin/Resources/config/config.yaml" }

```

Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

```bash
mkdir -p templates/bundles/SyliusAdminBundle/
cp -R vendor/prometee/sylius-vies-client-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
mkdir -p templates/bundles/SyliusShopBundle/
cp -R vendor/prometee/sylius-vies-client-plugin/src/Resources/views/SyliusShopBundle/* templates/bundles/SyliusShopBundle/
```

Update `Channel` entity : `src/Entity/Channel/Channel.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ChannelInterface
{
    use EuropeanChannelAwareTrait;
}
```

And the corresponding interface : `src/Entity/Channel/ChannelInterface.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface, EuropeanChannelAwareInterface
{
}
```

If you are using YAML orm definitions :

```yaml
# config/doctrine/Address.orm.yml

App\Entity\Adressing\Address :
    type: entity
    table: sylius_address

    fields:
        vatNumber:
            name: vat_number
            type: string
            nullable: true
```

Then change the default Sylius model class :

```yaml
# config/packages/sylius_channel.yaml

sylius_channel:
    resources:
        channel:
            classes:
                model: App\Entity\Channel\Channel
```

Update `Address` entity : `src/Entity/Addressing/Address.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Addressing;

use Doctrine\ORM\Mapping as ORM;
use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareTrait;
use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_address")
 */
class Address extends BaseAddress implements AddressInterface
{
    use VATNumberAwareTrait;
}
```

And the corresponding interface : `src/Entity/Addressing/AddressInterface.php`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Addressing;

use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface as BaseAddressInterface;

interface AddressInterface extends BaseAddressInterface, VATNumberAwareInterface
{
}
```
If you are using YAML orm definitions :

```yaml
# config/doctrine/Channel.orm.yml

App\Entity\Channel\Channel:
    type: entity
    table: sylius_channel

    manyToOne:
        baseCountry:
            targetEntity: Sylius\Component\Addressing\Model\CountryInterface
            fetch: EAGER
            joinColumn:
                name: base_country_id
                onDelete: "SET NULL"
        europeanZone:
            targetEntity: Sylius\Component\Addressing\Model\ZoneInterface
            fetch: EAGER
            joinColumn:
                name: european_zone_id
                onDelete: "SET NULL"
```

Then change the default Sylius model class :

```yaml
# config/packages/sylius_addressing.yaml

sylius_addressing:
    resources:
        address:
            classes:
                model: App\Entity\Addressing\Address
```

Update your database :

```bash
php ./bin/console doctrine:migrations:diff
php ./bin/console doctrine:migrations:migrate
```

Load some required fixtures :

```bash
./bin/console sylius:fixture:load european_vat_zones
````

Go to your admin panel and edit your `Channel` to set the two fields to indicate to this plugin :
1. What is your base country to compare to your customer country.
2. What is the European zone to know if the customer is part of the Europe or not.

## Fixtures

You can add some fixtures to auto-configure your channel, for example add this into a yaml file :

```yaml
# config/packages/my_fixtures.yaml

sylius_fixtures:
    suites:
    
        french_european_channel:    
        
            listeners:
                logger: ~
                
            fixtures:
                
                address_with_vat_number:
                    options:
                        custom:
                            my_customer:
                                first_name: "John"
                                last_name: "Doe"
                                phone_number: "+33912345678"
                                company: "My Company Inc."
                                street: "1234 Street Avenue"
                                city: "London"
                                postcode: "1234"
                                country_code: "GB"
                                customer: "john.doe@mycompany.com"
                                vat_number: ~ # could also be "GB123456789"
            
                vies_plugin_european_channel:
                    options:
                        custom:
                            default:
                                channel: "default_channel_code" # Put an existing Channel code
                                base_country: "FR" # Existing Country code
                                european_zone: "EU" # Existing Zone code
``` 

[ico-version]: https://img.shields.io/packagist/v/Prometee/sylius-vies-client-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Prometee/SyliusVIESClientPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Prometee/SyliusVIESClientPlugin.svg?style=flat-square
[ico-symfony-insight]: https://insight.symfony.com/projects/08c9f3db-7d34-487c-9c26-425a95fd9673/mini.svg

[link-packagist]: https://packagist.org/packages/prometee/sylius-vies-client-plugin
[link-travis]: https://travis-ci.org/Prometee/SyliusVIESClientPlugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin
[link-symfony-insight]: https://insight.symfony.com/projects/08c9f3db-7d34-487c-9c26-425a95fd9673
