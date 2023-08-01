<?php

namespace OHMedia\AntispamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const CAPTCHA_TYPE_HCAPTCHA = 'hcaptcha';
    public const CAPTCHA_TYPE_RECAPTCHA = 'recaptcha';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('oh_media_antispam');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('captcha')
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue(self::CAPTCHA_TYPE_RECAPTCHA)
                            ->validate()
                                ->ifNotInArray([self::CAPTCHA_TYPE_RECAPTCHA, self::CAPTCHA_TYPE_HCAPTCHA])
                                ->thenInvalid('Invalid captcha type %s')
                            ->end()
                        ->end()
                        ->scalarNode('sitekey')->end()
                        ->scalarNode('secretkey')->end()
                        ->scalarNode('theme')
                            ->defaultValue('light')
                            ->validate()
                                ->ifNotInArray(['light', 'dark'])
                                ->thenInvalid('Invalid captcha theme %s')
                            ->end()
                        ->end()
                        ->scalarNode('size')
                            ->defaultValue('normal')
                            ->validate()
                                ->ifNotInArray(['normal', 'compact'])
                                ->thenInvalid('Invalid captcha size %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
