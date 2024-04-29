<?php

namespace OHMedia\AntispamBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaAntispamBundle extends AbstractBundle
{
    public const CAPTCHA_TYPE_HCAPTCHA = 'hcaptcha';
    public const CAPTCHA_TYPE_RECAPTCHA = 'recaptcha';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
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
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import('../config/services.yaml');

        if (empty($config['captcha']['sitekey']) || empty($config['captcha']['secretkey'])) {
            if (self::CAPTCHA_TYPE_HCAPTCHA === $config['captcha']['type']) {
                $config['captcha']['sitekey'] = '10000000-ffff-ffff-ffff-000000000001';
                $config['captcha']['secretkey'] = '0x0000000000000000000000000000000000000000';
            } elseif (self::CAPTCHA_TYPE_RECAPTCHA === $config['captcha']['type']) {
                $config['captcha']['sitekey'] = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
                $config['captcha']['secretkey'] = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            }
        }

        $parameters = $containerConfigurator->parameters();

        foreach ($config['captcha'] as $key => $value) {
            $parameters
                ->set("oh_media_antispam.captcha.$key", $value)
            ;
        }

        $this->registerWidget($containerBuilder);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $containerBuilder)
    {
        $resource = '@OHMediaAntispam/Form/captcha_widget.html.twig';

        $containerBuilder->setParameter('twig.form.resources', array_merge(
            $containerBuilder->getParameter('twig.form.resources'),
            [$resource]
        ));
    }
}
