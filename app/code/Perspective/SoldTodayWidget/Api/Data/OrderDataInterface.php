<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Api\Data;

/**
 * Api Interface
 */
interface OrderDataInterface
{
    const string ID = 'id';
    const string ORDER_ID = 'order_id';
    const string PRODUCT_ID = 'product_id';
    const string SKU = 'sku';
    const string PRICE = 'price';
    const string QTY_ORDERED = 'qty_ordered';
    const string CREATED_AT = 'created_at';
    const string MAIN_TABLE = 'perspective_order_item_data';
    /**
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id): mixed;

    /**
     * @return mixed
     */
    public function getOrderId(): mixed;

    /**
     * @param $orderId
     * @return mixed
     */
    public function setOrderId($orderId): mixed;

    /**
     * @return mixed
     */
    public function getProductId(): mixed;

    /**
     * @param $productId
     * @return mixed
     */
    public function setProductId($productId): mixed;

    /**
     * @return mixed
     */
    public function getSku(): mixed;

    /**
     * @param $sku
     * @return mixed
     */
    public function setSku($sku): mixed;

    /**
     * @return mixed
     */
    public function getPrice(): mixed;

    /**
     * @param $price
     * @return mixed
     */
    public function setPrice($price): mixed;

    /**
     * @return mixed
     */
    public function getQtyOrdered(): mixed;

    /**
     * @param $qtyOrdered
     * @return mixed
     */
    public function setQtyOrdered($qtyOrdered): mixed;

    /**
     * @return mixed
     */
    public function getCreatedAt(): mixed;

    /**
     * @param $createdAt
     * @return mixed
     */
    public function setCreatedAt($createdAt): mixed;
}
