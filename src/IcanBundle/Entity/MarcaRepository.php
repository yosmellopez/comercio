<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MarcaRepository extends EntityRepository
{

    /**
     * ListarOrdenadas: Lista las marcas ordenadas
     *
     * @author Marcel
     */
    public function ListarOrdenadas()
    {
        $consulta = $this->createQueryBuilder('m')
            ->where('m.estado = 1')
            ->orderBy('m.nombre', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * BuscarPorImagen: Devuelve el slider de la imagen
     * @param string $imagen imagen
     *
     * @author Marcel
     */
    public function BuscarPorImagen($imagen)
    {
        $criteria = array('imagen' => $imagen);
        return $this->findOneBy($criteria);
    }

    /**
     * BuscarPorUrl: Devuelve el producto de la url
     * @param string $url url
     *
     * @author Marcel
     */
    public function BuscarPorUrl($url)
    {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }


    /**
     * ListarMarcas: Lista los marca
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarMarcas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('m');

        if ($sSearch != "")
            $consulta->andWhere('m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");

        $consulta->orderBy("m.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalMarcas: Total de marca de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalMarcas($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(m.marcaId) FROM IcanBundle\Entity\Marca m ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1) {
                $where .= 'WHERE m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion ';
            } else {
                $where .= 'AND m.nombre LIKE :nombre OR m.descripcion LIKE :descripcion ';
            }
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_nombre = substr_count($consulta, ':nombre');
        if ($esta_query_nombre == 1) {
            $query->setParameter('nombre', "%${sSearch}%");
        }

        $esta_query_descripcion = substr_count($consulta, ':descripcion');
        if ($esta_query_descripcion == 1) {
            $query->setParameter('descripcion', "%${sSearch}%");
        }

        $total = $query->getSingleScalarResult();
        return $total;
    }

}
