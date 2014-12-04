<?php

namespace Credibility\DandB\Models;

class DandBOrder extends BaseModel {

    private $products = array();
    private $paymentType;
    private $sendConfirmationEmail = false;
    private $agent;
    private $partnerIdentifier;
    private $orderLevelPromotionIdentifier;
    private $caseReferenceIdentifier;
    private $five9SessionIdentifier;
    private $paymentTypeCode;
    private $customerGroupDomainCode;


    /**
     * @param DandBProduct $product
     * @returns this
     */

    public function addProduct(DandBProduct $product) {
        $this->products[] = $product;
        return $this;
    }

    /**
     * @param string $type
     * return this;
     */
    public function setPaymentType($type) {
        $this->paymentType = $type;
        return $this;
    }

    /**
     * @param bool $sendEmail - defaults is false
     * @returns this
     */
    public function setSendConfirmationEmail(bool $sendEmail) {
        $this->sendConfirmationEmail = $sendEmail;
        return $this;
    }

    /**
     * @param DandBAgent $agent
     * @returns this
     */
    public function setAgent(DandBAgent $agent) {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @param mixed $partnerId
     * @returns this
     */

    public function setPartnerIdentifier($partnerId) {
        $this->partnerIdentifier = $partnerId;
        return $this;
    }

    /**
     * @param mixed $promotionIdentifier
     * @returns this
     */

    public function setOrderLevelPromotionIdentifier($promotionIdentifier) {
        $this->orderLevelPromotionIdentifier = $promotionIdentifier;
        return $this;
    }


    /**
     * @param mixed $caseReferenceIdentifier
     * @returns this
     */

    public function setCaseLevelIdentifier($caseReferenceIdentifier) {
        $this->caseReferenceIdentifier = $caseReferenceIdentifier;
        return $this;
    }


    /**
     * @param mixed $five9SessionIdentifier
     * @returns this
     */

    public function setFive9SessionIdentifier($five9SessionIdentifier) {
        $this->five9SessionIdentifier = $five9SessionIdentifier;
        return $this;
    }


    /**
     * @param mixed $paymentTypeCode
     * @returns this
     */

    public function setPaymentTypeCode($paymentTypeCode) {
        $this->paymentTypeCode = $paymentTypeCode;
        return $this;
    }


    /**
     * @param mixed $customerGroupDomainCode
     * @returns this
     */

    public function setCustomerGroupDomainCode($customerGroupDomainCode) {
        $this->customerGroupDomainCode = $customerGroupDomainCode;
        return $this;
    }


    /**
     * @returns array
     */

    public function getProducts() {
        return $this->products;
    }

    /**
     * @returns string|null
     */

    public function getPaymentType() {
        return $this->paymentType;
    }

    /**
     * returns DandbProduct|null
     */
    public function getFirstProduct() {
        return !empty($this->products[0]) ? $this->products[0] : null;
    }

    /**
     * @returns bool
     */
    public function getSendConfirmationEmail() {
        return $this->sendConfirmationEmail;
    }

    /**
     * @returns DandBAgent|null
     */
    public function getAgent() {
        return $this->agent;
    }

    /**
     * @returns mixed
     */

    public function getPartnerIdentifier() {
        return $this->partnerIdentifier;
    }

    /**
     * @returns mixed
     */

    public function getOrderLevelPromotionIdentifier() {
        return $this->orderLevelPromotionIdentifier;
    }


    /**
     * @returns mixed
     */

    public function getCaseLevelIdentifier() {
        return $this->caseReferenceIdentifier;
    }


    /**
     * @returns mixed
     */

    public function getFive9SessionIdentifier() {
        return $this->five9SessionIdentifier;
    }


    /**
     * @returns mixed
     */

    public function getPaymentTypeCode() {
        return $this->paymentTypeCode;
    }


    /**
     * @returns mixed
     */

    public function getCustomerGroupDomainCode() {
        return $this->customerGroupDomainCode;
    }


    /**
     * @returns array
     */
    public function getProductsArray() {
        $returnArray = array();
        $products = $this->getProducts();
        foreach ($products as $product) {
            $returnArray[] = array(
                'product_id' => $product->getProductId(),
                'price_id' => $product->getPriceId(),
                'quantity' => $product->getQuantity(),
                'duns' => $product->getDuns(),
                'promotion_identifier' => $product->getPromotionIdentifier(),
                'payment_sub_type_code' => $product->getPaymentSubTypeCode(),
                'payment_instrument_identifier' => $product->getPaymentInstrumentIdentifier()
            );
        }
        return $returnArray;
    }

    /**
     * @returns bool
     */
    public function hasAgent() {
        return !empty($this->agent);
    }



}