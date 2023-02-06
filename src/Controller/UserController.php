<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {

        // Get authenticated user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Removed loggedIn User
        $users = $userRepository->findByExcept($user->getId());

        $all_users = $paginator->paginate($users, $request->query->getInt('page', 1), 6);

        return $this->render('user/index.html.twig', [
            'users' => $all_users,
        ]);
    }

    /**
     * @Route("/profile", name="profile_edit", methods={"GET","POST"})
     */
    public function editProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder) :Response
    {

        // Get authenticated user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->remove('agreeTerms');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/filter/user", name="filter_user", methods={"POST"})
     */
    public function filterUser(Request $request, UserRepository $userRepository)
    {

        $name  = $request->get("user_name");
        $email  = $request->get("user_email");

        // Get authenticated user
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $usersRep = $userRepository->filterUser($name, $email, $user->getId());
        $users = [];

        foreach ($usersRep as $user) {

            array_push($users, [
                'id'    => $user->getId(),
                'name'  => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]);

        }

        return new JsonResponse([
            'users'     => $users,
        ]);

    }

}
