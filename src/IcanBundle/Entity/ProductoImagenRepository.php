<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;


class ProductoImagenRepository extends EntityRepository
{    
    
    /**
     * ListarImagenes: Lista las imagenes de un producto
     * @param int $producto_id  
     *
     * @author Marcel
     */
    public function ListarImagenes($producto_id)
    {
        $consulta = $this->createQueryBuilder('pi')
                    ->leftJoin('pi.producto', 'p')
                    ->where('p.productoId = :producto_id')
                    ->setParameter('producto_id', $producto_id)
                    ->getQuery();   
        
        $lista = $consulta->getResult();
        return $lista;
    }    
}