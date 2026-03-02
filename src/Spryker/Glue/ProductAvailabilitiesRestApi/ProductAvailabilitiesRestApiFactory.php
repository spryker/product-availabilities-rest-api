<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi;

use Spryker\Glue\Kernel\AbstractFactory;
use Spryker\Glue\ProductAvailabilitiesRestApi\Dependency\Client\ProductAvailabilitiesRestApiToAvailabilityStorageClientInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Dependency\Client\ProductAvailabilitiesRestApiToProductStorageClientInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\AbstractProductAvailability\AbstractProductAvailabilitiesReader;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\AbstractProductAvailability\AbstractProductAvailabilitiesReaderInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\ConcreteProductAvailability\ConcreteProductAvailabilitiesReader;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\ConcreteProductAvailability\ConcreteProductAvailabilitiesReaderInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Expander\AbstractProductAvailabilitiesRelationshipExpander;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Expander\AbstractProductAvailabilitiesRelationshipExpanderInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Expander\ConcreteProductAvailabilitiesRelationshipExpander;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Expander\ConcreteProductAvailabilitiesRelationshipExpanderInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\AbstractProductAvailabilitiesResourceMapper;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\AbstractProductAvailabilitiesResourceMapperInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\ConcreteProductAvailabilitiesResourceMapper;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\ConcreteProductAvailabilitiesResourceMapperInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\Wishlist\RestWishlistItemsMapper;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper\Wishlist\RestWishlistItemsMapperInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder\ProductAbstractAvailabilityRestResponseBuilder;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder\ProductAbstractAvailabilityRestResponseBuilderInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder\ProductConcreteAvailabilityRestResponseBuilder;
use Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder\ProductConcreteAvailabilityRestResponseBuilderInterface;

class ProductAvailabilitiesRestApiFactory extends AbstractFactory
{
    public function createAbstractProductAvailabilitiesReader(): AbstractProductAvailabilitiesReaderInterface
    {
        return new AbstractProductAvailabilitiesReader(
            $this->getProductStorageClient(),
            $this->getAvailabilityStorageClient(),
            $this->createProductAbstractAvailabilityRestResponseBuilder(),
        );
    }

    public function createConcreteProductsAvailabilitiesReader(): ConcreteProductAvailabilitiesReaderInterface
    {
        return new ConcreteProductAvailabilitiesReader(
            $this->getAvailabilityStorageClient(),
            $this->getProductStorageClient(),
            $this->createProductConcreteAvailabilityRestResponseBuilder(),
        );
    }

    public function createAbstractProductAvailabilitiesRelationshipExpander(): AbstractProductAvailabilitiesRelationshipExpanderInterface
    {
        return new AbstractProductAvailabilitiesRelationshipExpander($this->createAbstractProductAvailabilitiesReader());
    }

    public function createConcreteProductAvailabilitiesRelationshipExpander(): ConcreteProductAvailabilitiesRelationshipExpanderInterface
    {
        return new ConcreteProductAvailabilitiesRelationshipExpander($this->createConcreteProductsAvailabilitiesReader());
    }

    public function createAbstractProductsAvailabilitiesResourceMapper(): AbstractProductAvailabilitiesResourceMapperInterface
    {
        return new AbstractProductAvailabilitiesResourceMapper();
    }

    public function createProductConcreteAvailabilityRestResponseBuilder(): ProductConcreteAvailabilityRestResponseBuilderInterface
    {
        return new ProductConcreteAvailabilityRestResponseBuilder(
            $this->getResourceBuilder(),
            $this->createConcreteProductsAvailabilitiesResourceMapper(),
        );
    }

    public function createProductAbstractAvailabilityRestResponseBuilder(): ProductAbstractAvailabilityRestResponseBuilderInterface
    {
        return new ProductAbstractAvailabilityRestResponseBuilder(
            $this->getResourceBuilder(),
            $this->createAbstractProductsAvailabilitiesResourceMapper(),
        );
    }

    public function createConcreteProductsAvailabilitiesResourceMapper(): ConcreteProductAvailabilitiesResourceMapperInterface
    {
        return new ConcreteProductAvailabilitiesResourceMapper();
    }

    public function createRestWishlistItemsMapper(): RestWishlistItemsMapperInterface
    {
        return new RestWishlistItemsMapper();
    }

    public function getAvailabilityStorageClient(): ProductAvailabilitiesRestApiToAvailabilityStorageClientInterface
    {
        return $this->getProvidedDependency(ProductAvailabilitiesRestApiDependencyProvider::CLIENT_AVAILABILITY_STORAGE);
    }

    public function getProductStorageClient(): ProductAvailabilitiesRestApiToProductStorageClientInterface
    {
        return $this->getProvidedDependency(ProductAvailabilitiesRestApiDependencyProvider::CLIENT_PRODUCT_STORAGE);
    }
}
