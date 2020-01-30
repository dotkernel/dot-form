<?php
/**
 * @see https://github.com/dotkernel/dot-form/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-form/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Form\Factory;

use Dot\Mapper\Mapper\MapperManager;
use Psr\Container\ContainerInterface;
use Laminas\Form\ElementFactory;

/**
 * Class EntitySelectFactory
 * @package Admin\App\Form\Element
 */
class EntitySelectFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];
        $elementFactory = new ElementFactory();
        $options['mapper_manager'] = $container->get(MapperManager::class);

        /** @var \Interop\Container\ContainerInterface $container */
        return $elementFactory->__invoke($container, $requestedName, $options);
    }
}
