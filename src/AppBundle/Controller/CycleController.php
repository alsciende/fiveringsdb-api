<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Cycle;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of CyclesController
 *
 * @Route("/cycles", name="cycles_")
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class CycleController extends AbstractApiController
{
    /**
     * Get all Cycles
     * @Route("", name="list")
     * @Method("GET")
     */
    public function listAction (Request $request, EntityManagerInterface $entityManager)
    {
        $this->setPublic($request);

        return $this->success(
            $entityManager
                ->getRepository(Cycle::class)
                ->findAll(),
            [
                'Default',
            ]
        );
    }

    /**
     * Get one Cycle
     * @Route("/{id}", name="get")
     * @Method("GET")
     */
    public function getAction (Request $request, Cycle $cycle)
    {
        $this->setPublic($request);

        return $this->success(
            $cycle,
            [
                'Default',
            ]
        );
    }

    /**
     * Delete one Cycle
     * @Route("/{id}", name="delete")
     * @Method("DELETE")
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction (EntityManagerInterface $entityManager, Cycle $cycle)
    {
        $entityManager->remove($cycle);

        try {
            $entityManager->flush();
        } catch (DBALException $exception) {
            return $this->failure("cannot_delete","This record cannot be deleted.");
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
