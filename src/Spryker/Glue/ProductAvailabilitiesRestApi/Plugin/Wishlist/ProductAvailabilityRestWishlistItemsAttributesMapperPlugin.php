<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ProductAvailabilitiesRestApi\Plugin\Wishlist;

use Generated\Shared\Transfer\RestWishlistItemsAttributesTransfer;
use Generated\Shared\Transfer\WishlistItemTransfer;
use Spryker\Glue\Kernel\AbstractPlugin;
use Spryker\Glue\WishlistsRestApiExtension\Dependency\Plugin\RestWishlistItemsAttributesMapperPluginInterface;

/**
 * @method \Spryker\Glue\ProductAvailabilitiesRestApi\ProductAvailabilitiesRestApiFactory getFactory()
 */
class ProductAvailabilityRestWishlistItemsAttributesMapperPlugin extends AbstractPlugin implements RestWishlistItemsAttributesMapperPluginInterface
{
    /**
     * {@inheritDoc}
     * - Maps availability data to `RestWishlistItemsAttributes` transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\WishlistItemTransfer $wishlistItemTransfer
     * @param \Generated\Shared\Transfer\RestWishlistItemsAttributesTransfer $restWishlistItemsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\RestWishlistItemsAttributesTransfer
     */
    public function map(
        WishlistItemTransfer $wishlistItemTransfer,
        RestWishlistItemsAttributesTransfer $restWishlistItemsAttributesTransfer
    ): RestWishlistItemsAttributesTransfer {
        return $this->getFactory()
            ->createRestWishlistItemsMapper()
            ->mapWishlistItemTransferToRestWishlistItemsAttributesTransfer(
                $wishlistItemTransfer,
                $restWishlistItemsAttributesTransfer,
            );
    }
}
