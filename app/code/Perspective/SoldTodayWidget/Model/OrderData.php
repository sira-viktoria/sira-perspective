<?php
/**
 * Viktoriia Sira <viktoriia.s@perspectiveteam.com>
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Perspective\SoldTodayWidget\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Perspective\SoldTodayWidget\Api\Data\OrderDataInterface;
use Perspective\SoldTodayWidget\Model\ResourceModel\OrderData as ResourceModelOrderData;

/**
 * OrderData Class.
 */
class OrderData extends AbstractModel implements OrderDataInterface
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModelOrderData::class);
    }

    /**
     * @return array|mixed|null
     */
    public function getId(): mixed
    {
        return $this->getData(self::ID);
    }

    /**
     * @param $id
     * @return null|OrderData
     */
    public function setId($id): null|OrderData
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @return mixed
     */
    public function getOrderId(): mixed
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @param $orderId
     * @return null|OrderData
     */
    public function setOrderId($orderId): null|OrderData
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return mixed
     */
    public function getProductId(): mixed
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param $productId
     * @return null|OrderData
     */
    public function setProductId($productId): null|OrderData
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return array|mixed|null
     */
    public function getSku(): mixed
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param $sku
     * @return null|OrderData
     */
    public function setSku($sku): null|OrderData
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @return mixed
     */
    public function getPrice(): mixed
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @param $price
     * @return null|OrderData
     */
    public function setPrice($price): null|OrderData
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return mixed
     */
    public function getQtyOrdered(): mixed
    {
        return $this->getData(self::QTY_ORDERED);
    }

    /**
     * @param $qtyOrdered
     * @return null|OrderData
     */
    public function setQtyOrdered($qtyOrdered): null|OrderData
    {
        return $this->setData(self::QTY_ORDERED, $qtyOrdered);
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): mixed
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param $createdAt
     * @return null|OrderData
     */
    public function setCreatedAt($createdAt): null|OrderData
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
