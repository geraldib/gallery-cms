<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);

    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(
        PostRepository $postRepository,
        CommentRepository $commentRepository,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository): Response
    {

        $posts            = $postRepository->getMostLiked();
        $latestPosts      = $postRepository->getTheLatest();
        $latestComments   = $commentRepository->getTheLatest();
        $latestCategories = $categoryRepository->getTheLatest();
        $latestUsers      = $userRepository->getTheLatest();


        $postsNr          = count($latestPosts);
        $commentsNr       = count($latestComments);
        $categoriesNr     = count($latestCategories);
        $usersNr          = count($latestUsers);

        return $this->render('admin/dashboard.html.twig', [
            'posts'           => $posts,
            'postsNr'         => $postsNr,
            'commentsNr'      => $commentsNr,
            'categoriesNr'    => $categoriesNr,
            'usersNr'         => $usersNr,
            'controller_name' => 'AdminController',
        ]);

    }

}
