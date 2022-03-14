<?php

namespace OHMedia\AntispamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class OHMediaAntispamExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['recaptcha'] as $key => $value) {
            $container->setParameter("oh_media_antispam.recaptcha.$key", $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        $resource = '@OHMediaAntispam/Form/recaptcha_widget.html.twig';

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            [$resource]
        ));
    }
}