<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler;

class TwigTemplateCompilerPass extends AbstractTwigTemplateCompilerPass
{
    public function __construct($dir)
    {
        $this->loadOverridenBundlesDirectory($dir);
    }
}
