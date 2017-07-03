<?php

namespace PhpSolution\UserAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SonataUserExtension
 */
class UserAdminExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->setParameter('user_admin.admin_entity_class', $config['admin_entity_class']);
        $this->registerTokenType($config, $container);
        $this->registerResetPasswordNotifier($config, $container);

        if ($config['sonata_enabled']) {
            $loader->load('sonata.yaml');
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerTokenType(array $config, ContainerBuilder $container): void
    {
        $tokenConf = $config['reset_password_token'];
        if ($container->hasDefinition('user_admin.reset_process')) {
            $container->getDefinition('user_admin.reset_process')
                ->replaceArgument(2, $tokenConf['type_name']);
        }
        if ($container->hasDefinition('user_admin.reset_token_type')) {
            $container->getDefinition('user_admin.reset_token_type')
                ->replaceArgument(0, $tokenConf['type_name'])
                ->replaceArgument(1, $tokenConf['configuration'])
                ->replaceArgument(2, $tokenConf['options']);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerResetPasswordNotifier(array $config, ContainerBuilder $container): void
    {
        if ($container->hasDefinition('user_admin.reset_pass_notifier')) {
            $container->getDefinition('user_admin.reset_pass_notifier')
                ->setArgument(2, $config['reset_password_notifier']['msg_tmpl'])
                ->setArgument(3, $config['reset_password_notifier']['sender_email'])
                ->setArgument(4, $config['reset_password_notifier']['msg_title']);
        }
    }
}