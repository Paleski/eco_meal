<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\BusinessType;
use App\Form\BusinessFormType;
use App\Repository\BusinessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BusinessController extends AbstractController
{
    #[Route('/business', name: 'app_business')]
    public function index(BusinessRepository $businessRepository, Security $security): Response
    {
        if($this->isGranted('ROLE_BUSINESS')){
            $user = $security->getUser();
            $id = $user->getBusiness()->getId();

            return $this->redirectToRoute('app_business_view',[
                'id' => $id,
            ]);
        }else if(! $this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_denied');
        }
        $businesses = $businessRepository->findAll();

        return $this->render('business/index.html.twig', [
            'businesses' => $businesses,
        ]);
    }

    #[Route('/business/{id}', name: 'app_business_view')]
    public function view(Business $business, Security $security): Response
    {
        $user = $security->getUser();
        $id = $user->getBusiness()?->getId();


        if(! ($this->isGranted('ROLE_ADMIN') || ($user && $user->getBusiness() && $user->getBusiness()->getId() == $id)))
        {
            return $this->redirectToRoute('app_denied');
        }
        return $this->render('business/view.html.twig', [
            'business' => $business,
        ]);
    }
    #[Route('/new/business', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $business = new Business();
        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($business);
            $entityManager->flush();

            return $this->redirectToRoute('app_business');
        }
        return $this->render('business/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/business/delete/{id}', name: 'app_business_delete', methods: ['GET'])]
    public function delete(Request $request, Business $business, Security $security, EntityManagerInterface $entityManager): Response
    {

        $user = $security->getUser();
        $id = $user->getBusiness()->getId();

        if(! $this->isGranted('ROLE_ADMIN') || ($user && $user->getBusiness() && $user->getBusiness()->getId() == $id))
        {
            return $this->redirectToRoute('app_denied');
        }
        $entityManager->remove($business);
        $entityManager->flush();

        return $this->redirectToRoute('app_business');
    }
}
