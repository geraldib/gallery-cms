<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{

    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(Request $request,
                          CommentRepository $commentRepository,
                          UserInterface $user,
                          PaginatorInterface $paginator): Response
    {

        if (in_array("ROLE_ADMIN", $user->getRoles())){
            $comments = $commentRepository->getTheLatest();
        } else {
            $comments = $commentRepository->findCommentByUserId($user->getId());
        }

        $all_comments = $paginator->paginate($comments, $request->query->getInt('page', 1), 6);

        return $this->render('comment/index.html.twig', [
            'comments' => $all_comments,
        ]);

    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'post' => $comment,
            'form' => $form->createView(),
        ]);

    }

}