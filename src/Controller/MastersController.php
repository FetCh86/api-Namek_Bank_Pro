<?php


namespace App\Controller;

use App\Entity\Master;
use App\Repository\MasterRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class MastersController extends FOSRestController
{
    private $masterRepository;
    private $em;

    public function __construct(MasterRepository $masterRepository, EntityManagerInterface $em)
    {
        $this->masterRepository = $masterRepository;
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"master"})
     */
    public function getMastersAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $masters = $this->masterRepository->findAll();
        return $this->view($masters);
    } // "get_masters"              [GET] /masters

    /**
     * @Rest\View(serializerGroups={"master"})
     */
    public function getMasterAction(Master $master)
    {
        if(!$master){
            throw $this->createNotFoundException();
        }

        return $this->view($master);
    } // "get_master"               [GET] /masters/{id}

    /**
     * @Rest\View(serializerGroups={"master"})
     * @Rest\Post("/masters")
     * @ParamConverter("master", converter="fos_rest.request_body")
     */
    public function postMastersAction(Master $master)
    {
        $this->em->persist($master);
        $this->em->flush();
        return $this->view($master);
    } // "post_masters"
    //             [POST] /masters

    /**
     * @Rest\View(serializerGroups={"master"})
     */
    public function putMasterAction(Request $request, int $id)
    {
        $master = $this->masterRepository->find($id);

        if(!$master){
            throw $this->createNotFoundException();
        }

        if($master->getId() == $this->getUser()->getId() || in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            $fst = $request->get('firstname');
            $lst = $request->get('lastname');
            $eml = $request->get('email');

            if($fst){
                $master ->setFirstname($fst);
            }

            if($lst){
                $master->setLastname($lst);
            }

            if($eml){
                $master->setEmail($eml);
            }

            $this->em->persist($master);
            $this->em->flush();

            return $this->view($master);
        }
        else {
            throw $this->createAccessDeniedException();
        }

    } // "put_master"               [PUT] /masters/{id}

    /**
     * @Rest\View(serializerGroups={"master"})
     */
    public function deleteMasterAction(int $id)
    {
        $master = $this->masterRepository->find($id);

        if(!$master){
            throw $this->createNotFoundException();
        }

        if($master->getId() == $this->getUser()->getId() || in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            $this->em->remove($master);
            $this->em->flush();
        }
        else
        {
            throw $this->createAccessDeniedException();
        }
    } // "delete_master"            [DELETE] /masters/{id}
}