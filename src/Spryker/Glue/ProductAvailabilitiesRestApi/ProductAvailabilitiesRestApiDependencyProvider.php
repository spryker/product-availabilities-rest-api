<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi;

use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;
use Spryker\Glue\ProductAvailabilitiesRestApi\Dependency\Client\ProductAvailabilitiesRestApiToAvailabilityStorageClientBridge;
use Spryker\Glue\ProductAvailabilitiesRestApi\Dependency\Client\ProductAvailabilitiesRestApiToProductStorageClientBridge;

/**
 * @method \Spryker\Glue\ProductAvailabilitiesRestApi\ProductAvailabilitiesRestApiConfig getConfig()
 */
class ProductAvailabilitiesRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_AVAILABILITY_STORAGE = 'CLIENT_AVAILABILITY_STORAGE';

    /**
     * @var string
     */
    public const CLIENT_PRODUCT_STORAGE = 'CLIENT_PRODUCT_STORAGE';

    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addAvailabilityStorageClient($container);
        $container = $this->addProductStorageClient($container);

        return $container;
    }

    protected function addAvailabilityStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_AVAILABILITY_STORAGE, function (Container $container) {
            return new ProductAvailabilitiesRestApiToAvailabilityStorageClientBridge(
                $container->getLocator()->availabilityStorage()->client(),
            );
        });

        return $container;
    }

    protected function addProductStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_PRODUCT_STORAGE, function (Container $container) {
            return new ProductAvailabilitiesRestApiToProductStorageClientBridge($container->getLocator()->productStorage()->client());
        });

        return $container;
    }
}
