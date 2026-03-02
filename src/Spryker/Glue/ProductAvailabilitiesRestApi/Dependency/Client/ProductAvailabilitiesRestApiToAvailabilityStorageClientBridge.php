<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Dependency\Client;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;

class ProductAvailabilitiesRestApiToAvailabilityStorageClientBridge implements ProductAvailabilitiesRestApiToAvailabilityStorageClientInterface
{
    /**
     * @var \Spryker\Client\AvailabilityStorage\AvailabilityStorageClientInterface
     */
    protected $availabilityStorageClient;

    /**
     * @param \Spryker\Client\AvailabilityStorage\AvailabilityStorageClientInterface $availabilityStorageClient
     */
    public function __construct($availabilityStorageClient)
    {
        $this->availabilityStorageClient = $availabilityStorageClient;
    }

    public function findProductAbstractAvailability(int $idProductAbstract): ?ProductAbstractAvailabilityTransfer
    {
        return $this->availabilityStorageClient->findProductAbstractAvailability($idProductAbstract);
    }
}
