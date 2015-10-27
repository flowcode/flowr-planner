<?php

namespace Flower\PlannerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
	public function findByStartDate($owner,$from,$to,$limit,$offset){
		$qb = $this->createQueryBuilder("m")
          ->leftJoin("m.users", "u")
        	->andWhere('m.startDate >= :from' )
        	->andWhere('(m.owner = :owner) OR (u.id = :owner)' )
        	->setMaxResults($limit)
        	->setFirstResult($offset)
          ->orderBy("m.startDate",'asc');
        $qb->setParameter("owner", $owner);
        $qb->setParameter("from", $from);
       	if($to){
       		$qb->andWhere('m.startDate <= :to' )
       			->setParameter("to", $to);
       	}
        return $qb->getQuery()->getResult();;
	}
  public function search($completeText, $texts,$from, $user,$limit = 10)
    {
        $qb = $this->createQueryBuilder("e");
        $qb->orWhere("e.title like :text")
              ->leftJoin("e.users", "u")
              ->orWhere("e.description like :text")
              ->setParameter("text", "%".$completeText."%")
              ->andWhere('(e.owner = :owner) OR (u.id = :owner)' )
              ->andWhere('e.startDate >= :from' )
              ->setParameter("owner", $user)
              ->setMaxResults($limit);
        $qb->setParameter("from", $from);
        $result = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder("e");
        $qb->leftJoin("e.users", "u")
              ->andWhere('(e.owner = :owner) OR (u.id = :owner)' )
              ->andWhere('e.startDate >= :from' )
              ->setParameter("owner", $user)
              ->setParameter("from", $from)
              ->setMaxResults($limit);
        $query = "(";
        $countTasks = count($texts);
        $count = 0;
        foreach ($texts as $text) {
            $count ++;
            $query .= "e.title like :text_".$count." ";
            $qb->setParameter("text_".$count, "%".$text."%");
            if($countTasks > $count){
               $query .=" or ";
            }
        }
        $query .= ")";
        $qb->andWhere($query );
        $result = array_merge($result,$qb->getQuery()->getResult());
        return array_unique($result, SORT_REGULAR);
    }

}