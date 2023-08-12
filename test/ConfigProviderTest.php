<?php

declare(strict_types=1);

namespace DotTest\Form;

use Dot\Form\ConfigProvider;
use Dot\Form\Factory\FormAbstractServiceFactory;
use Dot\Form\FormElementManager;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasAbstractFactories(): void
    {
        $this->assertArrayHasKey('abstract_factories', $this->config['dependencies']);
        $this->assertContains(
            FormAbstractServiceFactory::class,
            $this->config['dependencies']['abstract_factories']
        );
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertArrayHasKey(FormElementManager::class, $this->config['dependencies']['aliases']);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertArrayHasKey(FormElementManager::class, $this->config['dependencies']['factories']);
    }
}
