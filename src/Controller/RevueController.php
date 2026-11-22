<?php

namespace App\Controller;

use App\Entity\Revue;
use App\Form\RevueType;
use App\Repository\RevueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/revue')]
class RevueController extends AbstractController
{
    #[Route('/', name: 'app_revue_index', methods: ['GET'])]
    public function index(Request $request, RevueRepository $revueRepository): Response
    {
        $keyword = $request->query->get('keyword', '');
        $revues  = $keyword
            ? $revueRepository->searchByKeyword($keyword)
            : $revueRepository->findAll();

        return $this->render('revue/index.html.twig', [
            'revues'  => $revues,
            'keyword' => $keyword,
        ]);
    }

    #[Route('/new', name: 'app_revue_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $revue = new Revue();
        $form  = $this->createForm(RevueType::class, $revue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $revue->setImage($newFilename);
            }

            $entityManager->persist($revue);
            $entityManager->flush();
            $this->addFlash('success', 'Revue ajoutée avec succès.');
            return $this->redirectToRoute('app_revue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('revue/new.html.twig', [
            'revue' => $revue,
            'form'  => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_revue_show', methods: ['GET'])]
    public function show(Revue $revue): Response
    {
        return $this->render('revue/show.html.twig', [
            'revue' => $revue,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_revue_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Revue $revue, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RevueType::class, $revue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile, $slugger);
                $revue->setImage($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Revue mise à jour.');
            return $this->redirectToRoute('app_revue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('revue/edit.html.twig', [
            'revue' => $revue,
            'form'  => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_revue_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Revue $revue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $revue->getId(), $request->request->get('_token'))) {
            $entityManager->remove($revue);
            $entityManager->flush();
            $this->addFlash('success', 'Revue supprimée.');
        }

        return $this->redirectToRoute('app_revue_index', [], Response::HTTP_SEE_OTHER);
    }
    private function uploadImage(UploadedFile $file, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/covers',
                $newFilename
            );
        } catch (FileException $e) {
            $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
        }

        return $newFilename;
    }
}
