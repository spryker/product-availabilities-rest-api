<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\ConcreteProductAvailabilitiesStorefrontResource;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\AvailabilityStorage\AvailabilityStorageClientInterface;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\ProductAvailabilitiesRestApiConfig;
use Spryker\Glue\ProductsRestApi\ProductsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class ConcreteProductAvailabilitiesStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string MAPPING_TYPE_SKU = 'sku';

    protected const string KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    protected const string URI_VAR_SKU = 'concreteProductSku';

    public function __construct(
        protected ProductStorageClientInterface $productStorageClient,
        protected AvailabilityStorageClientInterface $availabilityStorageClient,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return array<\Generated\Api\Storefront\ConcreteProductAvailabilitiesStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $sku = $this->resolveConcreteProductSku();

        $localeName = $this->getLocale()->getLocaleNameOrFail();
        $productConcreteData = $this->productStorageClient->findProductConcreteStorageDataByMapping(
            static::MAPPING_TYPE_SKU,
            $sku,
            $localeName,
        );

        if ($productConcreteData === null) {
            throw new GlueApiException(
                Response::HTTP_NOT_FOUND,
                ProductsRestApiConfig::RESPONSE_CODE_CANT_FIND_CONCRETE_PRODUCT,
                ProductsRestApiConfig::RESPONSE_DETAIL_CANT_FIND_CONCRETE_PRODUCT,
            );
        }

        $idProductAbstract = (int)($productConcreteData[static::KEY_ID_PRODUCT_ABSTRACT] ?? 0);
        $abstractAvailabilityTransfer = $this->availabilityStorageClient->findProductAbstractAvailability($idProductAbstract);

        if ($abstractAvailabilityTransfer !== null) {
            foreach ($abstractAvailabilityTransfer->getProductConcreteAvailabilities() as $concreteAvailability) {
                if ($concreteAvailability->getSku() === $sku) {
                    return [$this->mapToResource($sku, $concreteAvailability)];
                }
            }
        }

        throw new GlueApiException(
            Response::HTTP_NOT_FOUND,
            ProductAvailabilitiesRestApiConfig::RESPONSE_CODE_CONCRETE_PRODUCT_AVAILABILITY_NOT_FOUND,
            ProductAvailabilitiesRestApiConfig::RESPONSE_DETAILS_CONCRETE_PRODUCT_AVAILABILITY_NOT_FOUND,
        );
    }

    protected function resolveConcreteProductSku(): string
    {
        if (!$this->hasUriVariable(static::URI_VAR_SKU)) {
            $this->throwMissingConcreteProductSku();
        }

        $sku = (string)$this->getUriVariable(static::URI_VAR_SKU);

        if ($sku === '') {
            $this->throwMissingConcreteProductSku();
        }

        return $sku;
    }

    protected function throwMissingConcreteProductSku(): never
    {
        throw new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            ProductsRestApiConfig::RESPONSE_CODE_CONCRETE_PRODUCT_SKU_IS_NOT_SPECIFIED,
            ProductsRestApiConfig::RESPONSE_DETAIL_CONCRETE_PRODUCT_SKU_IS_NOT_SPECIFIED,
        );
    }

    protected function mapToResource(
        string $sku,
        ProductConcreteAvailabilityTransfer $transfer
    ): ConcreteProductAvailabilitiesStorefrontResource {
        $availability = $transfer->getAvailability();

        $resource = new ConcreteProductAvailabilitiesStorefrontResource();
        $resource->concreteProductSku = $sku;
        $resource->isNeverOutOfStock = (bool)$transfer->getIsNeverOutOfStock();
        $resource->quantity = $availability?->__toString();
        $resource->availability = ($availability !== null && $availability->greaterThan(0)) || (bool)$transfer->getIsNeverOutOfStock();

        return $resource;
    }
}
