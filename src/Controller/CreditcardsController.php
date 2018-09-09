<?php

namespace App\Controller;

use App\Entity\Creditcard;
use App\Repository\CreditcardRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class CreditcardsController extends FOSRestController
{
    private $creditcardRepository;
    private $em;

    public function __construct(CreditcardRepository $creditcardRepository, EntityManagerInterface $em)
    {
        $this->creditcardRepository = $creditcardRepository;
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"creditcard"})
     */
    public function getCreditcardsAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER' || 'ROLE_ADMIN');

        if(in_array('ROLE ADMIN', $this->getUser()->getRoles()))
        {
            $creditcards = $this->creditcardRepository->findAll();
        }
        else{
            $creditcards = $this->getUser()->getCompany()->getCreditcards();
        }

        return $this->view($creditcards);
    } // "get_creditcards"              [GET] /creditcards

    /**
     * @Rest\View(serializerGroups={"creditcard"})
     */
    public function getCreditcardAction(Creditcard $creditcard)
    {
        if(!$creditcard){
            throw $this->createNotFoundException();
        }

        return $this->view($creditcard);
    } // "get_creditcard"               [GET] /creditcards/{id}

    /**
     * @Rest\View(serializerGroups={"creditcard"})
     * @Rest\Post("/creditcards")
     * @ParamConverter("creditcard", converter="fos_rest.request_body")
     */
    public function postCreditcardsAction(Creditcard $creditcard)
    {
        $this->denyAccessUnlessGranted('ROLE_USER' || 'ROLE_ADMIN');

        $creditcard->setCompany($this->getUser()->getCompany());

        $this->em->persist($creditcard);
        $this->em->flush();

        return $this->view($creditcard);

    } // "post_creditcards"             [POST] /creditcards

    /**
     * @Rest\View(serializerGroups={"creditcard"})
     */
    public function putCreditcardAction(Request $request, int $id)
    {
        $creditcard = $this->creditcardRepository->find($id);

        if(!$creditcard){
            throw $this->createNotFoundException();
        }

        $master = $creditcard->getCompany()->getMaster();

        if($master->getId() == $this->getUser()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
        {
            $name = $request->get('name');
            $ccType = $request->get('creditCardType');
            $ccNumber = $request->get('creditCardNumber');

            if($name){
                $creditcard->setName($name);
            }

            if($ccType){
                $creditcard->setCreditCardType($ccType);
            }

            if($ccNumber){
                $creditcard->setCreditCardNumber($ccNumber);
            }
        }
        else{
            throw $this->createAccessDeniedException();
        }
    } // "put_creditcard"               [PUT] /creditcards/{id}

    /**
     * @Rest\View(serializerGroups={"creditcard"})
     */
    public function deleteCreditcardAction(int $id)
    {
        $creditcard = $this->creditcardRepository->find($id);

        if(!$creditcard){
            throw $this->createNotFoundException();
        }

        $master = $creditcard->getCompany()->getMaster();

        if($master->getId() == $this->getUser()->getId() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
        {
            $this->em->remove($creditcard);
            $this->em->flush();
        }
        else{
            throw $this->createAccessDeniedException();
        }
    } // "delete_creditcard"            [DELETE] /creditcards/{id}
}