<?php

namespace IcanBundle\Entity;

use Doctrine\ORM\EntityRepository;


class PorcientoRepository extends EntityRepository
{
    /**
     * ListarOrdenados: Lista los menu
     *
     * @author Marcel
     */
    public function ListarOrdenados()
    {
        $consulta = $this->createQueryBuilder('p');

        $consulta->orderBy('p.valor', 'ASC');

        $lista = $consulta->getQuery()->getResult();
        return $lista;
    }

    /**
     * BuscarPorValor: Devuelve el porciento al que le corresponde el valor
     * @param string $valor valor
     *
     * @author Marcel
     */
    public function BuscarPorValor($valor)
    {
        $criteria = array('valor' => $valor);
        return $this->findOneBy($criteria);
    }

    /**
     * ListarPorcientos: Lista los menu
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPorcientos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $consulta = $this->createQueryBuilder('p');

        if ($sSearch != "") {
            $consulta->andWhere('p.valor LIKE :valor')
                ->setParameter('valor', "%${sSearch}%");
        }

        $consulta->orderBy("p.$iSortCol_0", $sSortDir_0);

        if ($limit > 0) {
            $consulta->setMaxResults($limit);
        }

        $lista = $consulta->setFirstResult($start)
            ->getQuery()->getResult();
        return $lista;
    }

    /**
     * TotalPorcientos: Total de porcientos de la BD
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function TotalPorcientos($sSearch)
    {
        $em = $this->getEntityManager();
        $consulta = 'SELECT COUNT(p.porcientoId) FROM IcanBundle\Entity\Porciento p ';
        $join = '';
        $where = '';

        if ($sSearch != "") {
            $esta_query = explode("WHERE", $where);
            if (count($esta_query) == 1)
                $where .= 'WHERE p.valor LIKE :valor ';
            else
                $where .= 'AND p.valor LIKE :valor ';
        }

        $consulta .= $join;
        $consulta .= $where;
        $query = $em->createQuery($consulta);
        //Adicionar parametros        
        //$sSearch
        $esta_query_valor = substr_count($consulta, ':valor');
        if ($esta_query_valor == 1)
            $query->setParameter(':valor', $sSearch);

        $total = $query->getSingleScalarResult();
        return $total;
    }

}