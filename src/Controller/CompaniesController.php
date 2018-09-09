<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class CompaniesController extends FOSRestController
{

    private $companyRepository;
    private $em;

    public function __construct(CompanyRepository $companyRepository, EntityManagerInterface $em)
    {
        $this->companyRepository = $companyRepository;
        $this->em = $em;
    }

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function getCompaniesAction()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $companies = $this->companyRepository->findAll();
        return $this->view($companies);
    } // "get_companies"              [GET] /companies

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function getCompanyAction(Company $company)
    {
        if(!$company){
            throw $this->createNotFoundException();
        }

        return $this->view($company);
    } // "get_company"               [GET] /companies/{id}

    /**
     * @Rest\View(serializerGroups={"company"})
     * @Rest\Post("/companies")
     * @ParamConverter("company", converter="fos_rest.request_body")
     */
    public function postCompaniesAction(Company $company)
    {
        $master = $this->getUser();

        $company->setMaster($master);


        $this->em->persist($company);
        $this->em->flush();
        return $this->view($company);
    } // "post_companies"             [POST] /companies

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function putCompanyAction(Request $request, int $id)
    {
        $company = $this->companyRepository->find($id);

        $master = $company->getMaster();

        if(!$company){
            throw $this->createNotFoundException();
        }

        if($master->getId() == $this->getUser()->getId() || in_array("ROLE_USER", $this->getUser()->getRoles()))
        {
            $name = $request->get('name');
            $slg = $request->get('slogan');
            $phone = $request->get('phoneNumber');
            $add = $request->get('address');
            $web = $request->get('websiteUrl');
            $pic = $request->get('pictureUrl');

            if($name){
                $company->setName($name);
            }
            if($slg){
                $company->setSlogan($slg);
            }
            if($phone){
                $company->setPhoneNumber($phone);
            }
            if($add){
                $company->setAddress($add);
            }
            if($web){
                $company->setWebsiteUrl($name);
            }
            if($pic) {
                $company->setPictureUrl($pic);
            }
        }
        else {
            throw $this->createAccessDeniedException();
        }

    } // "put_company"               [PUT] /companies/{id}

    /**
     * @Rest\View(serializerGroups={"company"})
     */
    public function deleteCompanyAction(int $id)
    {
        $company = $this->companyRepository->find($id);

        $master = $company->getMaster();

        if(!$company){
            throw $this->createNotFoundException();
        }

        if($master->getId() == $this->getUser()->getId() || in_array("ROLE_USER", $this->getUser()->getRoles()))
        {
            $this->em->remove($company);
            $this->em->flush();
        }
        else {
            throw $this->createAccessDeniedException();
        }
    } // "delete_company"            [DELETE] /companies/{id}
}