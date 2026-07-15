<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Form\ConsumerFormType;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConsumerController extends AbstractController
{
    #[Route('/consumer', name: 'app_consumer')]
    public function index(ConsumerRepository $consumerRepository, Security $security): Response
    {
        if($this->isGranted('ROLE_CONSUMER'))
        {
            $user = $security->getUser();
            $id = $user->getConsumer()->getId();

            return $this->redirectToRoute('app_consumer_view',[
                'id' => $id,
            ]);
        }
        else if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $consumers = $consumerRepository->findAll();

        return $this->render('consumer/index.html.twig', [
            'consumers' => $consumers,
        ]);
    }

    #[Route('/consumer/{id}', name: 'app_consumer_view')]
    public function view(ConsumerRepository $consumerRepository, $id, Security $security): Response
    {
        $user = $security->getUser();

        if (($this->isGranted('ROLE_ADMIN')) || ($user && $user->getConsumer() && $user->getConsumer()->getId() == $id)) {
            return $this->render('consumer/view.html.twig', [
                'consumer' => $consumerRepository->find($id),
            ]);
        }
        return $this->redirectToRoute('app_denied');
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
    public function edit(Request $request, Consumer $consumer,Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $id = $user->getConsumer()?->getId();

        if ($user && $user->getConsumer() && $user->getConsumer()->getId() == $id) {
            $form = $this->createForm(ConsumerFormType::class, $consumer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_consumer');
            }

            return $this->render('consumer/edit.html.twig', [
                'form' => $form,
                'consumer' => $consumer,
            ]);
        }
        return $this->redirectToRoute('app_denied');
    }

    #[Route('/consumer/delete/{id}', name: 'app_consumer_delete', methods: ['GET'])]
    public function delete(Request $request, Consumer $consumer, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $id = $user->getConsumer()->getId();

        if ($user && $user->getConsumer() && $user->getConsumer()->getId() == $id) {
            $entityManager->remove($consumer);
            $entityManager->flush();

            return $this->redirectToRoute('app_consumer');
        }
        return $this->redirectToRoute('app_denied');
    }
}
