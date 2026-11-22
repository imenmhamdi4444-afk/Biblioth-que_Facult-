<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
class UserManagementController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'ASC']),
        ]);
    }

    /**
     * Promote a user to ROLE_ADMIN or demote back to ROLE_USER.
     */
    #[Route('/{id}/toggle-role', name: 'app_user_toggle_role', methods: ['POST'])]
    public function toggleRole(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('toggle-role' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_user_index');
        }

        // Prevent admin from demoting themselves
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('app_user_index');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            // Demote to regular user
            $user->setRoles([]);
            $this->addFlash('success', $user->getUsername() . ' est maintenant Utilisateur.');
        } else {
            // Promote to admin
            $user->setRoles(['ROLE_ADMIN']);
            $this->addFlash('success', $user->getUsername() . ' est maintenant Administrateur.');
        }

        $em->flush();
        return $this->redirectToRoute('app_user_index');
    }

    /**
     * Delete a user account.
     */
    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete-user' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_user_index');
        }

        // Prevent admin from deleting themselves
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_user_index');
        }

        $username = $user->getUsername();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Compte de ' . $username . ' supprimé.');
        return $this->redirectToRoute('app_user_index');
    }
}
