[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

# Sylius Plugin for VIES Client

This plugin is adding :

- two new attributes to the `Channel` entity allowing to know from
  which country your shop is from and what's the European zone to target
- VAT Number field to the `Address` entity
- validation over VIES API on this field
- the basic European rule allowing the seller not to charge
  VAT to foreign European customers who have provided a valid VAT number

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

Then change the default Sylius model class :

```yaml
# config/packages/sylius_channel.yaml

sylius_channel:
    resources:
        channel:
            classes:
                model: App\Entity\Channel
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

Then change the default Sylius model class :

```yaml
# config/packages/sylius_addressing.yaml

sylius_addressing:
    resources:
        address:
            classes:
                model: App\Entity\Address
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
                
            features:
                european_channel:
                    listeners:
                        logger: ~
                        fixtures:
                            vies_plugin_european_channel:
                                options:
                                    custom:
                                        default:
                                            code: "default_channel_code" # put an existing Channel code
                                            base_country: "FR" # Existing Country code
                                            european_zone: "EU" # existing Zone code
``` 

[ico-version]: https://img.shields.io/packagist/v/Prometee/sylius-vies-client-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Prometee/SyliusVIESClientPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Prometee/SyliusVIESClientPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/prometee/sylius-vies-client-plugin
[link-travis]: https://travis-ci.org/Prometee/SyliusVIESClientPlugin
[link-scrutinizer]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Prometee/SyliusVIESClientPlugin
