<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserPostLike;
use App\Form\PostLikeType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;


    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(Request $request,
                          PostRepository $postRepository,
                          UserInterface $user,
                          PaginatorInterface $paginator): Response
    {

        if (in_array("ROLE_ADMIN", $user->getRoles())){
            $posts = $postRepository->getTheLatest();
        } else {
            $posts = $postRepository->findPostByUserId($user->getId());
        }

        $all_posts = $paginator->paginate($posts, $request->query->getInt('page', 1), 6);

        return $this->render('post/index.html.twig', [
            'posts' => $all_posts,
        ]);

    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('post')["image"];
            $uploads_directory = $this->getParameter("uploads_directory");
            $filename = md5(uniqid()) . '.' .$file->guessExtension();
            $file->move($uploads_directory, $filename);

            $entityManager = $this->getDoctrine()->getManager();

            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $user = $this->getUser();

            $post->setLikes(0);
            $post->setImage($filename);
            $post->setUser($user);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post, UserInterface $user): Response
    {

        if ( in_array("ROLE_ADMIN", $user->getRoles()) || $post->getUser() == $user ){
            return $this->render('post/show.html.twig', [
                'post' => $post,
            ]);
        } else {
            return $this->redirectToRoute('post_index');
        }

    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }


    /**
     * @Route("/filter/post", name="filter_post", methods={"POST"})
     */
    public function filterUser(Request $request, PostRepository $postRepository)
    {

        $name  = $request->get("post_name");

        $postsRep = $postRepository->filterPost($name);
        $posts = [];

        foreach ($postsRep as $post) {

            array_push($posts, [
                'id'    => $post->getId(),
                'name'  => $post->getName(),
                'body'  => $post->getBody(),
                'image' => $post->getImage()
            ]);

        }

        return new JsonResponse([
            'posts'     => $posts,
        ]);

    }


}
