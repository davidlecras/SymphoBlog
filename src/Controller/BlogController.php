<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticlesRepository $repo)
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles'=> $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig',[
            'title'=>'Bienvenue dans ce Blog'
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Articles $article= null, Request $request)
    {
        if(!$article){
            $article = new Articles();
        }

        // $form =$this->createFormBuilder($article)
        //         ->add('title')
        //         ->add('content')
        //         ->add('img')
        //         ->getForm();
        $form= $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if (!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id'=> $article->getId()]);
        }

        return $this->render('blog/create.html.twig',[
            'formArticle'=>$form->createView(),
            'editMode'=>$article->getId() !== null,
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Articles $article, Request $request)
    {
        $comment= new Comment();
        $form= $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }
        return $this->render('blog/show.html.twig',[
            'article'=>$article,
            'commentForm'=> $form->createView()
        ]);
    }


}
