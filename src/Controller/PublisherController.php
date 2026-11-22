<?php

namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Repository\PublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/publisher')]
class PublisherController extends AbstractController
{
    #[Route('/', name: 'app_publisher_index', methods: ['GET'])]
    public function index(PublisherRepository $publisherRepository): Response
    {
        return $this->render('publisher/index.html.twig', [
            'publishers' => $publisherRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_publisher_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($publisher);
            $entityManager->flush();
            $this->addFlash('success', 'Maison d\'édition ajoutée avec succès.');
            return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publisher/new.html.twig', [
            'publisher' => $publisher,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_publisher_show', methods: ['GET'])]
    public function show(Publisher $publisher): Response
    {
        return $this->render('publisher/show.html.twig', [
            'publisher' => $publisher,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_publisher_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Publisher $publisher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Maison d\'édition mise à jour.');
            return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publisher/edit.html.twig', [
            'publisher' => $publisher,
            'form'      => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_publisher_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Publisher $publisher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $publisher->getId(), $request->request->get('_token'))) {
            // Prevent deletion if books or revues are linked
            if ($publisher->getBooks()->count() > 0 || $publisher->getRevues()->count() > 0) {
                $this->addFlash('danger', 'Impossible de supprimer cette maison d\'édition : elle est liée à des livres ou des revues.');
                return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
            }
            $entityManager->remove($publisher);
            $entityManager->flush();
            $this->addFlash('success', 'Maison d\'édition supprimée.');
        }

        return $this->redirectToRoute('app_publisher_index', [], Response::HTTP_SEE_OTHER);
    }
}
