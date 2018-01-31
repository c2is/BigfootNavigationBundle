<?php

namespace Bigfoot\Bundle\NavigationBundle\Entity\Menu;

use Gedmo\Sortable\Entity\Repository\SortableRepository as EntityRepository;

/**
 * ItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ItemRepository extends EntityRepository
{
    /**
     * @param $slug
     *
     * @return \Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item[]
     */
    public function findByMenuSlug($slug)
    {
        return $this->createQueryBuilder('e')
            ->join('e.menu', 'm')
            ->andWhere('m.slug = :slug')
            ->andWhere('e.parent IS NULL')
            ->setParameter(':slug', $slug)
            ->orderBy('e.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
