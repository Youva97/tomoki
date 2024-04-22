<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function myFindProductId($minPrice, $maxPrice)
    {

        $queryBuilder = $this->createQueryBuilder('p')
            /*             ->where('p.price >= :minPrice')
            ->setParameter('minPrice', $minPrice * 100)
            ->andWhere('p.price <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice * 100) */
            ->where('p.price >= :minPrice AND p.price <= :maxPrice')
            ->setParameters(['minPrice' => $minPrice * 100, 'maxPrice' => $maxPrice * 100])
            ->orderBy('p.price', 'DESC');
        // On récupère la requête DQL
        $query = $queryBuilder->getQuery();
        // On récupère les résultat
        $result = $query->getResult();

        return $result;
    }

    public function findWithSearch($search)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->addSelect('category')
            ->join('p.category', 'category');

        if (count($search->getCategories()) > 0) {
            $queryBuilder
                ->andWhere('category.id IN (:categories)')
                ->setParameter('categories', $search->getCategories());
        }

        if (!empty($search->getString())) {

            $mots = explode(" ", $search->getString());

            foreach ($mots as $key => $mot) {
                $queryBuilder
                    ->andWhere('p.name LIKE :name' . $key . ' OR p.description LIKE :name' . $key . ' OR p.subtitle LIKE :name' . $key)
                    ->setParameter('name' . $key, '%' . $mot . '%');
            }
        }

        $query = $queryBuilder->getQuery();
        $result = $query->getResult();
        return $result;
    }


    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
