<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TokenRepository extends EntityRepository
{

    /**
     * ListarOrdenados: Lista los tokens ordenados por fecha
     *
     * @author Yosmel
     */
    public function ListarOrdenados() {
        $consulta = $this->createQueryBuilder('p')
            ->where('p.estado = 1')
            ->orderBy('p.fechapublicacion', 'DESC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }


    /**
     * BuscarPorUrl: Devuelve el token de la url
     * @param string $url url
     *
     * @author Yosmel
     */
    public function BuscarPorUrl($url) {
        $criteria = array('url' => $url);
        return $this->findOneBy($criteria);
    }


    /**
     * ListarTokensOrdenados: Lista los tokens ordenados por fecha
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarTokensOrdenados($start, $limit) {
        $consulta = $this->createQueryBuilder('p')->orderBy('p.fechapublicacion', 'DESC');
        $lista = $consulta->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * ListarTokens: Lista los token
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Yosmel
     */
    public function ListarTokens($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id = "", $marca_id = "") {
        $consulta = $this->createQueryBuilder('p');

        if ($sSearch != "") {
            $consulta
                ->andWhere('p.nombre LIKE :nombre OR p.descripcion LIKE :descripcion')
                ->setParameter('nombre', "%${sSearch}%")
                ->setParameter('descripcion', "%${sSearch}%");
        }

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }
}
