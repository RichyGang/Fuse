<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategoryAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryAttribute[]    findAll()
 * @method CategoryAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryAttribute::class);
    }

    public function findByIdJoinedToCategory(int $category): ?array
    {

        $sql = 'SELECT (SELECT id FROM category_attribute WHERE id = category_attribute_category.category_attribute_id) as attr_id, (SELECT name FROM category_attribute WHERE id = category_attribute_category.category_attribute_id) as attr_name, (SELECT unity FROM category_attribute WHERE id = category_attribute_category.category_attribute_id) as attr_unity, category_id FROM category_attribute_category WHERE category_attribute_category.category_id = :id';
        $params = array(
            'id' => $category,
        );

        $result  = $this->getEntityManager()->getConnection()->executeQuery($sql, $params)->fetchAllAssociative();
        dump("C LE RESULTE");
        dump($result);

        return $result;

    }

    // /**
    //  * @return CategoryAttribute[] Returns an array of CategoryAttribute objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryAttribute
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
