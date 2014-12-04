<?php

namespace Credibility\DandB\Models;

class DandBProduct extends BaseModel {

    private $productId;
    private $priceId;
    private $quantity = 1;
    private $duns;
    private $promotionIdentifier;
    private $paymentSubTypeCode;
    private $paymentInstrumentIdentifier;



    /**
     * @param mixed $productId
     * @returns this
     */

    public function setProductId($productId) {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param mixed $priceId
     * @returns this
     */

    public function setPriceId($priceId) {
        $this->priceId = $priceId;
        return $this;
    }

    /**
     * @param mixed $quantity
     * @returns this
     */

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
        return $this;
    }


    /**
     * @param mixed $duns
     * @returns this
     */

    public function setDuns($duns) {
        $this->duns = $duns;
        return $this;
    }

    /**
     * @param mixed $promotionIdentifier
     * @returns this
     */

    public function setPromotionIdentifier($promotionIdentifier) {
        $this->promotionIdentifier = $promotionIdentifier;
        return $this;
    }

    /**
     * @param mixed $paymentSubTypeCode
     * @returns this
     */

    public function setPaymentSubTypeCode($paymentSubTypeCode) {
        $this->paymentSubTypeCode = $paymentSubTypeCode;
        return $this;
    }

    /**
     * @param mixed $paymentInstrumentIdentifier
     * @returns this
     */

    public function setPaymentInstrumentIdentifier($paymentInstrumentIdentifier) {
        $this->paymentInstrumentIdentifier = $paymentInstrumentIdentifier;
        return $this;
    }




    /**
     * @returns mixed
     */

    public function getProductId() {
        return $this->productId;
    }

    /**
     * @returns mixed
     */

    public function getPriceId() {
        return $this->priceId;
    }

    /**
     * @returns mixed
     */

    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @returns mixed
     */

    public function getDuns() {
        return $this->duns;
    }

    /**
     * @returns mixed
     */

    public function getPromotionIdentifier() {
        return $this->promotionIdentifier;
    }

    /**
     * @returns mixed
     */

    public function getPaymentSubTypeCode() {
        return $this->paymentSubTypeCode;
    }

    /**
     * @returns mixed
     */

    public function getPaymentInstrumentIdentifier() {
        return $this->paymentInstrumentIdentifier;
    }




}