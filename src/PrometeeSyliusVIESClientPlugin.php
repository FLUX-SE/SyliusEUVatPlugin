<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin;

use Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler\OrderItemsBasedStrategyCompilerPass;
use Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler\TwigTemplateCompilerPass;
use Prometee\SyliusVIESClientPlugin\DependencyInjection\PrometeeSyliusVIESClientExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PrometeeSyliusVIESClientPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigTemplateCompilerPass(__DIR__ . '/Resources/views/overrides'));
        $container->addCompilerPass(new OrderItemsBasedStrategyCompilerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        // return the right extension instead of "auto-registering" it. Now the
        // alias can be flux_sylius_pace_oauth instead of flux_sylius_pace_o_auth
        if (null === $this->extension) {
            return new PrometeeSyliusVIESClientExtension();
        }

        return $this->extension;
    }
}
