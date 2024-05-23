<?php

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function myFindBy($offset, $limit)
    {
        $query = $this->createQueryBuilder('p')->where('p.active = 1'); // produits activés 
        // on range par date 
        $query->orderBy('p.startDate', 'asc'); // limites de la pagination: setFirstResult (qui fixe l’offset, à partir duquel on récupère) et setMaxResult(qui fixe la limite du nombre d'éléments récupérés). 
        $query->setFirstResult($offset)->setMaxResults($limit);
        $paginator = new Paginator($query, true);
        return $paginator;
    }
}
