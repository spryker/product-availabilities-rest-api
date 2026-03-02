<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\RestAbstractProductAvailabilityAttributesTransfer;

interface AbstractProductAvailabilitiesResourceMapperInterface
{
    public function mapProductAbstractAvailabilityTransferToRestAbstractProductAvailabilityAttributesTransfer(
        ProductAbstractAvailabilityTransfer $productAbstractAvailabilityTransfer,
        RestAbstractProductAvailabilityAttributesTransfer $restAbstractProductAvailabilityAttributesTransfer
    ): RestAbstractProductAvailabilityAttributesTransfer;
}
