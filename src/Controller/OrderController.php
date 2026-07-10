<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderFormType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
    if(! $this->isGranted('ROLE_ADMIN'))
    {
        return $this->redirectToRoute('app_denied');
    }
        $orders = $orderRepository->findAll();

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_view')]
    public function view(Order $order): Response
    {
            if(! $this->isGranted('ROLE_ADMIN'))
            {
                return $this->redirectToRoute('app_denied');
            }
        return $this->render('order/view.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/new/order', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $order = new Order();
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/order/edit/{id}', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if(! $this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('app_denied');
        }
        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order');
        }

        return $this->render('order/edit.html.twig', [
            'form' => $form,
            'order' => $order,
        ]);
    }

    #[Route('/order/delete/{id}', name: 'app_order_delete', methods: ['GET'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_order');
    }
}
