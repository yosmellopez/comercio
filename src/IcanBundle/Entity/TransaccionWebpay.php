<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransaccionWebpay
 *
 * @ORM\Table(name="transaccion_webpay")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\TransaccionWebpayRepository")
 */
class TransaccionWebpay
{

    /**
     * @var integer
     *
     * @ORM\Column(name="transaccion_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $transaccionId;

    /**
     * @var string
     *
     * @ORM\Column(name="transactionDate", type="datetime", nullable=false)
     */
    private $transactionDate;

    /**
     * @var string
     *
     * @ORM\Column(name="cardNumber", type="string", length=16, nullable=false)
     */
    private $cardNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="cardExpirationDate", type="string", length=4, nullable=false)
     */
    private $cardExpirationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="authorizationCode", type="string", length=6, nullable=false)
     */
    private $authorizationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentTypeCode", type="string", length=2, nullable=false)
     */
    private $paymentTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="responseCode", type="string", length=255, nullable=false)
     */
    private $responseCode;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="sharesAmount", type="float", nullable=true)
     */
    private $sharesAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="sharesNumber", type="integer", nullable=true)
     */
    private $sharesNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="commerceCode", type="string", length=12, nullable=false)
     */
    private $commerceCode;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text",  nullable=false)
     */
    private $token;

    /**
     * @var \Cotizacion
     *
     * @ORM\ManyToOne(targetEntity="Cotizacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cotizacion_id", referencedColumnName="cotizacion_id")
     * })
     */
    private $cotizacion;


    /**
     * Get transaccionId
     *
     * @return integer
     */
    public function getTransaccionId()
    {
        return $this->transaccionId;
    }

    /**
     * Set transactionDate
     *
     * @param datetime $transactionDate
     * @return TransaccionWebpay
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;

        return $this;
    }

    /**
     * Get transactionDate
     *
     * @return datetime
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * Set cardNumber
     *
     * @param string $cardNumber
     * @return TransaccionWebpay
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * Get cardNumber
     *
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set cardExpirationDate
     *
     * @param string $cardExpirationDate
     * @return TransaccionWebpay
     */
    public function setCardExpirationDate($cardExpirationDate)
    {
        $this->cardExpirationDate = $cardExpirationDate;

        return $this;
    }

    /**
     * Get cardExpirationDate
     *
     * @return string
     */
    public function getCardExpirationDate()
    {
        return $this->cardExpirationDate;
    }

    /**
     * Set authorizationCode
     *
     * @param string $authorizationCode
     * @return WebpayConfig
     */
    public function setAuthorizationCode($authorizationCode)
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    /**
     * Get authorizationCode
     *
     * @return string
     */
    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    /**
     * Set paymentTypeCode
     *
     * @param string $paymentTypeCode
     * @return WebpayConfig
     */
    public function setPaymentTypeCode($paymentTypeCode)
    {
        $this->paymentTypeCode = $paymentTypeCode;

        return $this;
    }

    /**
     * Get paymentTypeCode
     *
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->paymentTypeCode;
    }

    /**
     * Set responseCode
     *
     * @param string $responseCode
     * @return WebpayConfig
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Get responseCode
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return WebpayConfig
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set sharesAmount
     *
     * @param float $sharesAmount
     * @return WebpayConfig
     */
    public function setSharesAmount($sharesAmount)
    {
        $this->sharesAmount = $sharesAmount;

        return $this;
    }

    /**
     * Get sharesAmount
     *
     * @return float
     */
    public function getSharesAmount()
    {
        return $this->sharesAmount;
    }

    /**
     * Set sharesNumber
     *
     * @param int $sharesNumber
     * @return WebpayConfig
     */
    public function setSharesNumber($sharesNumber)
    {
        $this->sharesNumber = $sharesNumber;

        return $this;
    }

    /**
     * Get sharesNumber
     *
     * @return int
     */
    public function getSharesNumber()
    {
        return $this->sharesNumber;
    }

    /**
     * Set commerceCode
     *
     * @param string $commerceCode
     * @return WebpayConfig
     */
    public function setCommerceCode($commerceCode)
    {
        $this->commerceCode = $commerceCode;

        return $this;
    }

    /**
     * Get commerceCode
     *
     * @return string
     */
    public function getCommerceCode()
    {
        return $this->commerceCode;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return TransaccionWebpay
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set cotizacion
     *
     * @param \IcanBundle\Entity\Cotizacion $cotizacion
     * @return Cotizacion
     */
    public function setCotizacion(\IcanBundle\Entity\Cotizacion $cotizacion = null)
    {
        $this->cotizacion = $cotizacion;

        return $this;
    }

    /**
     * Get cotizacion
     *
     * @return \IcanBundle\Entity\Cotizacion
     */
    public function getCotizacion()
    {
        return $this->cotizacion;
    }
}
