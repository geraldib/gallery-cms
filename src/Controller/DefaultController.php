<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserPostLike;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserPostLikeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="default", methods={"GET", "POST"})
     */
    public function index(
        Request $request,
        PostRepository $postRepository,
        Security $security,
        UserPostLikeRepository $userPostLikeRepository,
        PaginatorInterface $paginator): Response
    {

        $slider_posts = $postRepository->getSliderPosts();
        $posts        = $postRepository->getTheLatest();
        $all_posts    = $paginator->paginate($posts, $request->query->getInt('page', 1), 6);

        $user = $security->getUser();

        if (is_null($user)){
            $userLikes = [];
        } else {
            $user_id  = $user->getId();
            $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
            $userLikes = $userPostLikeRepository->userLikes($user);
        }

        return $this->render('index.html.twig', [
            "posts"        => $posts,
            "slider_posts" => $slider_posts,
            "all_posts"    => $all_posts,
            "user_likes"   => $userLikes
        ]);

    }

    /**
     * @Route("/like/post", name="like_post", methods={"POST"})
     */
    public function postLike(Request $request, UserPostLikeRepository $userPostLikeRepository, UserInterface $user)
    {


        $post_id  = $request->get("post_id");
        $user_id  = $user->getId();

        $post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);
        $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);

        $entityManager = $this->getDoctrine()->getManager();
        $userPostLikes = $userPostLikeRepository->isLikedPost($user_id, $post_id);

        if (empty($userPostLikes)){
            $is_liked = true;
            $newUserPostLikes = new UserPostLike();
            $newUserPostLikes->setPost($post);
            $newUserPostLikes->setUser($user);
            $newUserPostLikes->setIsLiked($is_liked);
            $entityManager->persist($newUserPostLikes);

            $post->setLikes($post->getLikes() + 1);
        } else {

            if ($userPostLikes[0]->getIsLiked() == true){
                $is_liked = false;
                $post->setLikes($post->getLikes() - 1);
            } else {
                $is_liked = true;
                $post->setLikes($post->getLikes() + 1);
            }
            $userPostLikes[0]->setIsLiked($is_liked);
        }

        $entityManager->flush();

        $userPostLikes = $userPostLikeRepository->isLikedPost($user_id, $post_id);

        return new JsonResponse([
            'postId'     => $post_id,
            'postLikes'  => $post->getLikes(),
            "user_likes" => $userPostLikes[0]->getIsLiked()
        ]);


    }

    /**
     * @Route("/single/post/{id}", name="single_post")
     */
    public function readMore(
        Request $request,
        UserPostLikeRepository $userPostLikeRepository,
        CommentRepository $commentRepository,
        Security $user) :Response
    {

        $post_id  = $request->get("id");
        $post = $this->getDoctrine()->getRepository(Post::class)->find($post_id);

        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['post' => $post]);

        $user_id  = $user->getUser();

        if (is_null($user_id)){
            $userLikes = [];
            $loginUserId = null;
        } else {
            $user = $this->getDoctrine()->getRepository(User::class)->find($user_id);
            $userLikes = $userPostLikeRepository->userLikes($user);
            $loginUserId = $user_id->getId();
        }

        return $this->render('single.html.twig', [
            "post"        => $post,
            "user_likes"  => $userLikes,
            "comments"    => $comments,
            "userId"      => $loginUserId
        ]);

    }

    /**
     * @Route("/comment/post", name="comment_post", methods={"POST"})
     */
    public function postComment(Request $request)
    {

        $commentMsg = $request->get("commentMsg");
        $userId = $request->get("userId");
        $postId = $request->get("postId");

        $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if($userId == null){
            throw new Exception('No user logged in!');
        }

        $comment = new Comment();
        $entityManager = $this->getDoctrine()->getManager();
        $comment->setUser($user);
        $comment->setPost($post);
        $comment->setComment($commentMsg);
        $entityManager->persist($comment);
        $entityManager->flush();

        return new JsonResponse([
            'commentMsg' => $commentMsg,
            'userName'   => $user->getName(),
            'commentId'    => $comment->getId()
        ]);
    }

    /**
     * @Route("/comment/delete", name="comment_delete", methods={"POST"})
     */
    public function deleteComment(Request $request)
    {

        $commentId = $request->get("comment_id");
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($commentId);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($comment);
        $entityManager->flush();

        return new JsonResponse([
            'message'     => "The comment was deleted!",
        ]);

    }

}
