<?php

/*
 * Copyright (c) 2025 Martin Pettersson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace N7e\WordPress;

use N7e\Configuration\ConfigurationInterface;
use N7e\DependencyInjection\ContainerBuilderInterface;
use N7e\DependencyInjection\ContainerInterface;
use N7e\ServiceProviderInterface;

/**
 * Provides WordPress admin menus.
 */
final class AdminMenuProvider implements ServiceProviderInterface
{
    /**
     * Registered admin menu pages.
     *
     * @var \N7e\WordPress\AdminMenu\PageRegistry
     */
    private readonly AdminMenu\PageRegistry $pages;

    /**
     * Create a new service provider instance.
     */
    public function __construct()
    {
        $this->pages = new AdminMenu\PageRegistry();
    }

    /**
     * {@inheritDoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function configure(ContainerBuilderInterface $containerBuilder): void
    {
    }

    /** {@inheritDoc} */
    public function load(ContainerInterface $container): void
    {
        /**
         * @var \N7e\Configuration\ConfigurationInterface $configuration
         */
        $configuration = $container->get(ConfigurationInterface::class);

        foreach ($configuration->get('adminMenu.pages', []) as $pageClass) {
            $this->pages->register($container->construct($pageClass));
        }
    }
}
