<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;

class ExceptionCheckerRepository extends EntityRepository
{
    //Finds by url
    public function findByUrl($url)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e')
            ->where('e.url LIKE :url')
            ->setParameter('url', $url . '%')
            ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    //Finds all the wildcards available
    public function findWildcard()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e')
            ->where('e.url LIKE :url')
            ->setParameter('url', '%*')
            ;

        return $qb->getQuery()->getResult();
    }
}