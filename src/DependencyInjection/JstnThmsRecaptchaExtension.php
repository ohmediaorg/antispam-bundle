<?php

namespace JstnThms\AntispamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class JstnThmsAntispamExtension extends Extension
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
            $container->setParameter("jstnthms_antispam.recaptcha.$key", $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        //$templating_engines = $container->getParameter('templating.engines');

        //if (in_array('twig', $templating_engines)) {
            $resource = '@JstnThmsAntispam/Form/recaptcha_widget.html.twig';

            $container->setParameter('twig.form.resources', array_merge(
                $container->getParameter('twig.form.resources'),
                [$resource]
            ));
        //}
    }
}