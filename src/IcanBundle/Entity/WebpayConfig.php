<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebpayConfig
 *
 * @ORM\Table(name="webpay_config")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\WebpayConfigRepository")
 */
class WebpayConfig
{

    /**
     * @var integer
     *
     * @ORM\Column(name="webpay_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $webpayId;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="text", nullable=false)
     */
    private $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="private_cert", type="text", nullable=false)
     */
    private $privateCert;

    /**
     * @var string
     *
     * @ORM\Column(name="public_cert", type="text", nullable=false)
     */
    private $publicCert;

    /**
     * @var string
     *
     * @ORM\Column(name="comercio_code", type="string", length=255, nullable=false)
     */
    private $comercioCode;

    /**
     * Get webpayId
     *
     * @return integer
     */
    public function getWebpayId()
    {
        return $this->webpayId;
    }

    /**
     * Set privateKey
     *
     * @param string $privateKey
     * @return WebpayConfig
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * Get privateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set privateCert
     *
     * @param string $privateCert
     * @return WebpayConfig
     */
    public function setPrivateCert($privateCert)
    {
        $this->privateCert = $privateCert;

        return $this;
    }

    /**
     * Get privateCert
     *
     * @return string
     */
    public function getPrivateCert()
    {
        return $this->privateCert;
    }

    /**
     * Set publicCert
     *
     * @param string $publicCert
     * @return WebpayConfig
     */
    public function setPublicCert($publicCert)
    {
        $this->publicCert = $publicCert;

        return $this;
    }

    /**
     * Get publicCert
     *
     * @return string
     */
    public function getPublicCert()
    {
        return $this->publicCert;
    }

    /**
     * Set comercioCode
     *
     * @param string $comercioCode
     * @return WebpayConfig
     */
    public function setComercioCode($comercioCode)
    {
        $this->comercioCode = $comercioCode;

        return $this;
    }

    /**
     * Get comercioCode
     *
     * @return string
     */
    public function getComercioCode()
    {
        return $this->comercioCode;
    }
}
