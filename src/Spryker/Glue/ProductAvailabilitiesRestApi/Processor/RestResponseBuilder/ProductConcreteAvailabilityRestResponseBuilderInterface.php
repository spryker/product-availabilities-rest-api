<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Processor\RestResponseBuilder;

use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface ProductConcreteAvailabilityRestResponseBuilderInterface
{
    public function createProductConcreteAvailabilityResource(
        ProductConcreteAvailabilityTransfer $productConcreteAvailabilityTransfer
    ): RestResourceInterface;

    public function createProductConcreteAvailabilityResponse(RestResourceInterface $productConcreteAvailabilityRestResource): RestResponseInterface;

    public function createProductConcreteSkuIsNotSpecifiedErrorResponse(): RestResponseInterface;

    public function createProductConcreteAvailabilityNotFoundErrorResponse(): RestResponseInterface;
}
