<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface ProductAbstractAvailabilityRestResponseBuilderInterface
{
    public function createProductAbstractAvailabilityResource(
        string $productAbstractSku,
        ProductAbstractAvailabilityTransfer $productAbstractAvailabilityTransfer
    ): RestResourceInterface;

    public function createProductAbstractAvailabilityResponse(RestResourceInterface $productAbstractAvailabilityRestResource): RestResponseInterface;

    public function createProductAbstractSkuIsNotSpecifiedErrorResponse(): RestResponseInterface;

    public function createProductAbstractAvailabilityNotFoundErrorResponse(): RestResponseInterface;
}
