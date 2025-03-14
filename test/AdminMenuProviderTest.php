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
use N7e\WordPress\AdminMenu\Page;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(AdminMenuProvider::class)]
class AdminMenuProviderTest extends TestCase
{
    use PHPMock;

    private AdminMenuProvider $provider;
    private MockObject $containerMock;
    private MockObject $configurationMock;

    #[Before]
    public function setUp(): void
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $this->configurationMock = $this->getMockBuilder(ConfigurationInterface::class)->getMock();
        $this->provider = new AdminMenuProvider();

        $this->containerMock->method('get')
            ->with(ConfigurationInterface::class)
            ->willReturn($this->configurationMock);
    }

    #[Test]
    public function shouldNotConfigureContainerBuilder(): void
    {
        $containerBuilderMock = $this->getMockBuilder(ContainerBuilderInterface::class)->getMock();
        $containerBuilderMock->expects($this->never())->method($this->anything());

        $this->provider->configure($containerBuilderMock);
    }

    #[Test]
    public function shouldNotRegisterAdminMenuPagesIfConfigurationIsEmpty(): void
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('get')
            ->with('adminMenu.pages', [])
            ->willReturn([]);
        $this->containerMock->expects($this->never())->method('construct');

        $this->provider->load($this->containerMock);
    }

    #[Test]
    public function shouldRegisterAdminMenuPageClassesFromConfiguration(): void
    {
        $this->getFunctionMock(__NAMESPACE__ . '\\AdminMenu', 'add_action')
            ->expects($this->once())->with($this->anything(), $this->anything());

        $pageMock = $this->getMockBuilder(Page::class)->disableOriginalConstructor()->getMock();

        $this->configurationMock
            ->expects($this->once())
            ->method('get')
            ->with('adminMenu.pages', [])
            ->willReturn(['class']);
        $this->containerMock
            ->expects($this->once())
            ->method('construct')
            ->with('class')
            ->willReturn($pageMock);

        $this->provider->load($this->containerMock);
    }
}
