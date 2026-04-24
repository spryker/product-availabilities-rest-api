<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\AbstractProductAvailabilitiesStorefrontResource;
use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\AvailabilityStorage\AvailabilityStorageClientInterface;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\ProductAvailabilitiesRestApiConfig;
use Spryker\Glue\ProductsRestApi\ProductsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class AbstractProductAvailabilitiesStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string MAPPING_TYPE_SKU = 'sku';

    protected const string KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    protected const string URI_VAR_ABSTRACT_PRODUCT_SKU = 'abstractProductSku';

    public function __construct(
        protected ProductStorageClientInterface $productStorageClient,
        protected AvailabilityStorageClientInterface $availabilityStorageClient,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return array<\Generated\Api\Storefront\AbstractProductAvailabilitiesStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $sku = $this->resolveAbstractProductSku();
        $localeName = $this->getLocale()->getLocaleNameOrFail();

        $productAbstractData = $this->productStorageClient->findProductAbstractStorageDataByMapping(
            static::MAPPING_TYPE_SKU,
            $sku,
            $localeName,
        );

        if ($productAbstractData === null) {
            throw new GlueApiException(
                Response::HTTP_NOT_FOUND,
                ProductAvailabilitiesRestApiConfig::RESPONSE_CODE_ABSTRACT_PRODUCT_AVAILABILITY_NOT_FOUND,
                ProductAvailabilitiesRestApiConfig::RESPONSE_DETAILS_ABSTRACT_PRODUCT_AVAILABILITY_NOT_FOUND,
            );
        }

        $idProductAbstract = (int)($productAbstractData[static::KEY_ID_PRODUCT_ABSTRACT] ?? 0);
        $availabilityTransfer = $this->availabilityStorageClient->findProductAbstractAvailability($idProductAbstract);

        if ($availabilityTransfer === null) {
            throw new GlueApiException(
                Response::HTTP_NOT_FOUND,
                ProductAvailabilitiesRestApiConfig::RESPONSE_CODE_ABSTRACT_PRODUCT_AVAILABILITY_NOT_FOUND,
                ProductAvailabilitiesRestApiConfig::RESPONSE_DETAILS_ABSTRACT_PRODUCT_AVAILABILITY_NOT_FOUND,
            );
        }

        return [$this->mapAvailabilityToResource($sku, $availabilityTransfer)];
    }

    protected function resolveAbstractProductSku(): string
    {
        if (!$this->hasUriVariable(static::URI_VAR_ABSTRACT_PRODUCT_SKU)) {
            $this->throwMissingAbstractProductSku();
        }

        $sku = (string)$this->getUriVariable(static::URI_VAR_ABSTRACT_PRODUCT_SKU);

        if ($sku === '') {
            $this->throwMissingAbstractProductSku();
        }

        return $sku;
    }

    protected function throwMissingAbstractProductSku(): never
    {
        throw new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            ProductsRestApiConfig::RESPONSE_CODE_ABSTRACT_PRODUCT_SKU_IS_NOT_SPECIFIED,
            ProductsRestApiConfig::RESPONSE_DETAIL_ABSTRACT_PRODUCT_SKU_IS_NOT_SPECIFIED,
        );
    }

    protected function mapAvailabilityToResource(
        string $abstractProductSku,
        ProductAbstractAvailabilityTransfer $availabilityTransfer
    ): AbstractProductAvailabilitiesStorefrontResource {
        $resource = new AbstractProductAvailabilitiesStorefrontResource();
        $resource->abstractProductSku = $abstractProductSku;
        $resource->quantity = $availabilityTransfer->getAvailability()?->__toString();
        $resource->availability = $this->isAvailable($availabilityTransfer);

        return $resource;
    }

    protected function isAvailable(ProductAbstractAvailabilityTransfer $availabilityTransfer): bool
    {
        $availability = $availabilityTransfer->getAvailability();

        if ($availability !== null && $availability->greaterThan(0)) {
            return true;
        }

        foreach ($availabilityTransfer->getProductConcreteAvailabilities() as $concreteAvailability) {
            if ($concreteAvailability->getIsNeverOutOfStock()) {
                return true;
            }
        }

        return false;
    }
}
