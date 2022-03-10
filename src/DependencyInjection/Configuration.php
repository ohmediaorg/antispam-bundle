<?php

namespace JstnThms\AntispamBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jstnthms_antispam');
        
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('recaptcha')
                    ->children()
                        ->scalarNode('sitekey')->isRequired()->end()
                        ->scalarNode('secretkey')->isRequired()->end()
                        ->scalarNode('theme')
                            ->defaultValue('light')
                            ->validate()
                                ->ifNotInArray(['light', 'dark'])
                                ->thenInvalid('Invalid reCAPTCHA theme %s')
                            ->end()
                        ->end()
                        ->scalarNode('size')
                            ->defaultValue('normal')
                            ->validate()
                                ->ifNotInArray(['normal', 'compact'])
                                ->thenInvalid('Invalid reCAPTCHA size %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
