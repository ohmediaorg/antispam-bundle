<?php

namespace OHMedia\AntispamBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class OHMediaAntispamExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (empty($config['captcha']['sitekey']) || empty($config['captcha']['secretkey'])) {
            if (Configuration::CAPTCHA_TYPE_HCAPTCHA === $config['captcha']['type']) {
                $config['captcha']['sitekey'] = '10000000-ffff-ffff-ffff-000000000001';
                $config['captcha']['secretkey'] = '0x0000000000000000000000000000000000000000';
            } elseif (Configuration::CAPTCHA_TYPE_RECAPTCHA === $config['captcha']['type']) {
                $config['captcha']['sitekey'] = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
                $config['captcha']['secretkey'] = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            }
        }

        foreach ($config['captcha'] as $key => $value) {
            $container->setParameter("oh_media_antispam.captcha.$key", $value);
        }

        $this->registerWidget($container);
    }

    /**
     * Registers the form widget.
     */
    protected function registerWidget(ContainerBuilder $container)
    {
        $resource = '@OHMediaAntispam/Form/captcha_widget.html.twig';

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            [$resource]
        ));
    }
}
