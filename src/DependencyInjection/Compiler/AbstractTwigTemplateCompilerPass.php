<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractTwigTemplateCompilerPass implements CompilerPassInterface
{
    protected $overridden_bundles_views = [];

    public function loadOverridenBundlesDirectory($dir)
    {
        foreach (scandir($dir) as $item) {
            $path = $dir . '/' . $item;
            if (preg_match('#(Bundle|Plugin)$#', $item) && is_dir($path)) {
                $this->overridden_bundles_views[$path] = preg_replace('#(Bundle)$#', '', $item);
            }
        }
    }

    public function process(ContainerBuilder $container)
    {
        if ($this->overridden_bundles_views) {
            $loader = $container->getDefinition('twig.loader.filesystem');

            //Backup the calls
            $calls = $loader->getMethodCalls();

            $call0 = null;
            //Backup the first addPath because is normally the main one
            foreach ($calls as $i => $call) {
                if ($call[0] === 'addPath') {
                    $call0 = $call;
                    unset($calls[$i]);

                    break;
                }
            }

            foreach ($this->overridden_bundles_views as $path => $twig_name) {
                array_unshift($calls, ['addPath', [$path, $twig_name]]);
            }

            //Readd main call
            if ($call0 !== null) {
                array_unshift($calls, $call0);
            }

            //Save and replace new calls
            $loader->setMethodCalls($calls);
        }
    }
}
