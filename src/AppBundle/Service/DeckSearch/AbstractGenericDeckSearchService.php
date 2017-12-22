<?php

namespace AppBundle\Service\DeckSearch;

use AppBundle\Entity\Deck;
use AppBundle\Search\DeckSearch;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of AbstractGenericDeckSearchService
 *
 * @author Alsciende <alsciende@icloud.com>
 */
abstract class AbstractGenericDeckSearchService extends AbstractDeckSearchService
{
    abstract public function getOrderedQuery (QueryBuilder $qb): Query;

    public function search (DeckSearch $search)
    {
        $query = $this
            ->getBaseQueryBuilder($search)
            ->select('COUNT(d)')
            ->getQuery();

        $query = $this
            ->getParametrizedQuery($query, $search);

        $search->setTotal((int) $query->getSingleScalarResult());

        $qb = $this
            ->getBaseQueryBuilder($search)
            ->select('d, u, COUNT(DISTINCT l.user) nbLikes, COUNT(DISTINCT c.id) nbComments')
            ->join('d.user', 'u')
            ->leftJoin('d.deckLikes', 'l')
            ->leftJoin('d.comments', 'c')
            ->groupBy('d, u');

        $query = $this
            ->getParametrizedQuery(
                $this->getOrderedQuery($qb),
                $search
            )
            ->setFirstResult($search->getFirstIndex())
            ->setMaxResults($search->getLimit());

        foreach ($query->getResult() as $result) {
            /** @var Deck $deck */
            $deck = $result[0];
            $deck->setNbLikes((int) $result['nbLikes']);
            $deck->setNbComments((int) $result['nbComments']);
            $search->addRecord($deck);
        }
    }

    private function getBaseQueryBuilder (DeckSearch $search): QueryBuilder
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->from('AppBundle:Deck', 'd')
            ->where('d.published = :published');

        if ($search->getSince() instanceof \DateTime) {
            $qb->andWhere('d.publishedAt >= :date');
        }

        if ($search->getPrimaryClan() !== null) {
            $qb->andWhere('d.primaryClan = :primaryClan');
        }

        if ($search->getSecondaryClan() !== null) {
            $qb->andWhere('d.secondaryClan = :secondaryClan');
        }

        if ($search->isFeatured()) {
            $qb->innerJoin('AppBundle:Feature', 'f', Query\Expr\Join::WITH, 'f.deck = d');
        }

        if (count($search->getCards()) > 0) {
            foreach ($search->getCards() as $index => $card) {
                $qb->andWhere($qb->expr()->exists(
                    $qb
                        ->getEntityManager()
                        ->createQueryBuilder()
                        ->select("dc$index")
                        ->from('AppBundle:DeckCard', "dc$index")
                        ->where("dc$index.card = :card$index")
                        ->andWhere("dc$index.deck = d")
                        ->getDQL()
                ));
            }
        }

        return $qb;
    }

    private function getParametrizedQuery (Query $query, DeckSearch $search): Query
    {
        $query->setParameter('published', true);

        if ($search->getSince() instanceof \DateTime) {
            $query->setParameter('date', $search->getSince());
        }

        if ($search->getPrimaryClan() !== null) {
            $query->setParameter('primaryClan', $search->getPrimaryClan());
        }

        if ($search->getSecondaryClan() !== null) {
            $query->setParameter('secondaryClan', $search->getSecondaryClan());
        }

        if (count($search->getCards()) > 0) {
            foreach ($search->getCards() as $index => $card) {
                $query->setParameter("card$index", $card);
            }
        }

        return $query;
    }
}