<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_view')]
    public function view(Category $category): Response
    {
        if(! $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BUSINESS'))
        {
            return $this->redirectToRoute('app_denied');
        }
        return $this->render('category/view.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/new/category', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/edit/{id}', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Nu este necesar persist() pentru un obiect deja existent adus de Doctrine
            $entityManager->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete', methods: ['GET'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_category');
    }
}
