<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Form\ConsumerFormType;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConsumerController extends AbstractController
{
    #[Route('/consumer', name: 'app_consumer')]
    public function index(ConsumerRepository $consumerRepository): Response
    {
        $consumers = $consumerRepository->findAll();

        return $this->render('consumer/index.html.twig', [
            'consumers' => $consumers,
        ]);
    }

    #[Route('/consumer/{id}', name: 'app_consumer_view')]
    public function view(Consumer $consumer): Response
    {
        return $this->render('consumer/view.html.twig', [
            'consumer' => $consumer,
        ]);
    }

    #[Route('/new/consumer', name: 'app_consumer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consumer = new Consumer();
        $form = $this->createForm(ConsumerFormType::class, $consumer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consumer);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('consumer/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/consumer/edit/{id}', name: 'app_consumer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consumer $consumer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsumerFormType::class, $consumer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }

        return $this->render('consumer/edit.html.twig', [
            'form' => $form,
            'consumer' => $consumer,
        ]);
    }

    #[Route('/consumer/delete/{id}', name: 'app_consumer_delete', methods: ['GET'])]
    public function delete(Request $request, Consumer $consumer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($consumer);
        $entityManager->flush();

        return $this->redirectToRoute('app_consumer');
    }
}
