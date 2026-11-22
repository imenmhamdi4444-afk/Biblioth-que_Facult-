<?php

namespace App\Controller;

use App\Entity\Borrowing;
use App\Entity\User;
use App\Form\BorrowingType;
use App\Repository\BorrowingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/borrowing')]
class BorrowingController extends AbstractController
{
    #[Route('/', name: 'app_borrowing_index', methods: ['GET'])]
    public function index(BorrowingRepository $borrowingRepository): Response
    {
        return $this->render('borrowing/index.html.twig', [
            'borrowings' => $borrowingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_borrowing_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        // If regular user, pre-set their student profile and hide the student field
        $borrowing = new Borrowing();
        if (!$isAdmin && $user->getStudent()) {
            $borrowing->setStudent($user->getStudent());
        }

        $form = $this->createForm(BorrowingType::class, $borrowing, [
            'is_admin' => $isAdmin,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // For regular users, force their own student profile (security: prevent tampering)
            if (!$isAdmin && $user->getStudent()) {
                $borrowing->setStudent($user->getStudent());
            }

            // Validate: exactly one document must be selected
            if ($borrowing->getBook() && $borrowing->getRevue()) {
                $this->addFlash('danger', 'Veuillez sélectionner un seul document (livre OU revue, pas les deux).');
                return $this->renderForm('borrowing/new.html.twig', [
                    'borrowing' => $borrowing,
                    'form'      => $form,
                ]);
            }

            if (!$borrowing->getBook() && !$borrowing->getRevue()) {
                $this->addFlash('danger', 'Veuillez sélectionner au moins un document (livre ou revue).');
                return $this->renderForm('borrowing/new.html.twig', [
                    'borrowing' => $borrowing,
                    'form'      => $form,
                ]);
            }

            // Check availability
            $target    = $borrowing->getBook() ?? $borrowing->getRevue();
            $available = $target->getNbCopiesAcquired() - $target->getNbCopiesBorrowed();
            if ($available <= 0) {
                $this->addFlash('danger', 'Désolé, ce document n\'est plus disponible (stock épuisé).');
                return $this->renderForm('borrowing/new.html.twig', [
                    'borrowing' => $borrowing,
                    'form'      => $form,
                ]);
            }

            $borrowing->setBookReturned(false);
            if (!$borrowing->getDateBorrowed()) {
                $borrowing->setDateBorrowed(new \DateTime());
            }

            $entityManager->persist($borrowing);
            $target->setNbCopiesBorrowed($target->getNbCopiesBorrowed() + 1);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunt enregistré avec succès.');
            return $this->redirectToRoute('app_borrowing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('borrowing/new.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_borrowing_show', methods: ['GET'])]
    public function show(Borrowing $borrowing): Response
    {
        return $this->render('borrowing/show.html.twig', [
            'borrowing' => $borrowing,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_borrowing_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Borrowing $borrowing, EntityManagerInterface $entityManager): Response
    {
        $wasReturned = $borrowing->isBookReturned();

        $form = $this->createForm(BorrowingType::class, $borrowing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If it was NOT returned before, but now it IS returned → decrement stock and set dateReturned
            if (!$wasReturned && $borrowing->isBookReturned()) {
                $target = $borrowing->getBook() ?? $borrowing->getRevue();
                if ($target && $target->getNbCopiesBorrowed() > 0) {
                    $target->setNbCopiesBorrowed($target->getNbCopiesBorrowed() - 1);
                }
                if (!$borrowing->getDateReturned()) {
                    $borrowing->setDateReturned(new \DateTime());
                }
                $this->addFlash('success', 'Document marqué comme retourné. Stock mis à jour.');
            } else {
                $this->addFlash('success', 'Prêt mis à jour.');
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_borrowing_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('borrowing/edit.html.twig', [
            'borrowing' => $borrowing,
            'form' => $form,
        ]);
    }

    /**
     * Quick return action — marks a borrowing as returned in one click.
     */
    #[Route('/{id}/return', name: 'app_borrowing_return', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function returnDocument(Request $request, Borrowing $borrowing, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('return' . $borrowing->getId(), $request->request->get('_token'))) {
            if (!$borrowing->isBookReturned()) {
                $borrowing->setBookReturned(true);
                $borrowing->setDateReturned(new \DateTime());

                $target = $borrowing->getBook() ?? $borrowing->getRevue();
                if ($target && $target->getNbCopiesBorrowed() > 0) {
                    $target->setNbCopiesBorrowed($target->getNbCopiesBorrowed() - 1);
                }

                $entityManager->flush();
                $this->addFlash('success', 'Document retourné avec succès. Stock mis à jour.');
            } else {
                $this->addFlash('info', 'Ce document a déjà été retourné.');
            }
        }

        return $this->redirectToRoute('app_borrowing_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_borrowing_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Borrowing $borrowing, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $borrowing->getId(), $request->request->get('_token'))) {
            // If not yet returned, decrement stock before deleting
            if (!$borrowing->isBookReturned()) {
                $target = $borrowing->getBook() ?? $borrowing->getRevue();
                if ($target && $target->getNbCopiesBorrowed() > 0) {
                    $target->setNbCopiesBorrowed($target->getNbCopiesBorrowed() - 1);
                }
            }
            $entityManager->remove($borrowing);
            $entityManager->flush();
            $this->addFlash('success', 'Prêt supprimé.');
        }

        return $this->redirectToRoute('app_borrowing_index', [], Response::HTTP_SEE_OTHER);
    }
}
