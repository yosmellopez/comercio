<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeoOnPage
 *
 * @ORM\Table(name="seo_on_page")
 * @ORM\Entity(repositoryClass="IcanBundle\Entity\SeoOnPageRepository")
 */
class SeoOnPage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="pagina_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $paginaId;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=60, nullable=true)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=160, nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="text", nullable=false)
     */
    private $tags;


    /**
     * Get paginaId
     *
     * @return integer
     */
    public function getPaginaId()
    {
        return $this->paginaId;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Pagina
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Slider
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Pagina
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Set tags
     *
     * @param boolean $tags
     * @return Pagina
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return boolean
     */
    public function getTags()
    {
        return $this->tags;
    }

}