<?php

namespace PhpSolution\UserAdminBundle\DependencyInjection;

use PhpSolution\JwtBundle\Jwt\Type\ConfigurableType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('user_admin');
        $rootNode
            ->children()
                ->booleanNode('sonata_enabled')->defaultValue(class_exists('Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle'))->end()
                ->scalarNode('admin_entity_class')->isRequired()->end()
                ->arrayNode('reset_password_notifier')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('sender_email')->defaultNull()->end()
                        ->scalarNode('msg_tmpl')->defaultValue('UserAdminBundle:Notification:password_reset.html.twig')->end()
                        ->scalarNode('msg_title')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('reset_password_token')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type_name')->defaultValue('user_admin.reset_password')->end()
                        ->scalarNode('configuration')->defaultValue('default')->end()
                        ->scalarNode('options')->defaultValue([ConfigurableType::OPTION_EXP => 1800])->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}