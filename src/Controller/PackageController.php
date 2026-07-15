<?php

namespace App\Controller;

use App\Dto\PackageSearchFilter;
use App\Entity\Business;
use App\Entity\Package;
use App\Form\PackageFiltersType;
use App\Form\PackageFormType;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/package', name: 'app_package')]
    public function index(Request $request,PackageRepository $packageRepository): Response
    {
        $packages = $packageRepository->findAll();

        $filter = new PackageSearchFilter();
        $form = $this->createForm(PackageFiltersType::class, $filter);
        $form->handleRequest($request);
        return $this->render('package/index.html.twig', [
            'packages' => $packages,
            'package_filter_form' => $form->createView(),
        ]);
    }

    #[Route('/package/{id}', name: 'app_package_view')]
    public function view(Package $package): Response
    {
        return $this->render('package/view.html.twig', [
            'package' => $package,
        ]);
    }
    #[Route('/new/business/{id}/package', name: 'app_package_new_for_business', methods: ['GET', 'POST'])]
    public function newPackageForBusiness(Request $request, Business $business, EntityManagerInterface $entityManager): Response
    {
        $package = new Package();

        $package->setBusiness($business);
        $package->setCreatedAt(new \DateTimeImmutable()); // Business-ul nu ar pune pachete mai vechi de o zi sau din viitor

        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($package);
            $entityManager->flush();

            return $this->redirectToRoute('app_business_view', ['id' => $business->getId()]);
        }

        return $this->render('package/new.html.twig', [
            'form' => $form,
            'business' => $business,
        ]);
    }

    #[Route('/package/edit/{id}', name: 'app_package_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Package $package, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_business_view', ['id' => $package->getBusiness()->getId()]);
        }

        return $this->render('package/edit.html.twig', [
            'form' => $form,
            'package' => $package,
        ]);
    }

    #[Route('/order/delete/{id}', name: 'app_package_delete', methods: ['GET'])]
    public function delete(Request $request, Package $package, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($package);
        $entityManager->flush();

        return $this->redirectToRoute('app_package');
    }
}
