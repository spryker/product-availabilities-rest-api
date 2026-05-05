<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\ConcreteProductAvailabilitiesStorefrontResource;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\AvailabilityStorage\AvailabilityStorageClientInterface;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Glue\ProductAvailabilitiesRestApi\Api\Storefront\Exception\ProductAvailabilitiesExceptionFactory;

class ConcreteProductAvailabilitiesStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string MAPPING_TYPE_SKU = 'sku';

    protected const string KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    protected const string URI_VAR_SKU = 'concreteProductSku';

    public function __construct(
        protected ProductStorageClientInterface $productStorageClient,
        protected AvailabilityStorageClientInterface $availabilityStorageClient,
        protected ProductAvailabilitiesExceptionFactory $exceptionFactory = new ProductAvailabilitiesExceptionFactory(),
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

        // Legacy BC: when the concrete product is not found, always answer 306
        // "Availability is not found" rather than 302 "Concrete product is not found".
        // See ConcreteProductAvailabilitiesReader::findConcreteProductAvailabilityBySku()
        // which returns null and the controller wraps it into a 306 response.
        if ($productConcreteData !== null) {
            $idProductAbstract = (int)($productConcreteData[static::KEY_ID_PRODUCT_ABSTRACT] ?? 0);
            $abstractAvailabilityTransfer = $this->availabilityStorageClient->findProductAbstractAvailability($idProductAbstract);

            if ($abstractAvailabilityTransfer !== null) {
                foreach ($abstractAvailabilityTransfer->getProductConcreteAvailabilities() as $concreteAvailability) {
                    if ($concreteAvailability->getSku() === $sku) {
                        return [$this->mapToResource($sku, $concreteAvailability)];
                    }
                }
            }
        }

        throw $this->exceptionFactory->createConcreteProductAvailabilityNotFoundException();
    }

    protected function resolveConcreteProductSku(): string
    {
        if (!$this->hasUriVariable(static::URI_VAR_SKU)) {
            throw $this->exceptionFactory->createMissingConcreteProductSkuException();
        }

        $sku = (string)$this->getUriVariable(static::URI_VAR_SKU);

        if ($sku === '') {
            throw $this->exceptionFactory->createMissingConcreteProductSkuException();
        }

        return $sku;
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
