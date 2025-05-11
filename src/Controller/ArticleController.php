<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Entity\PropertySearch; 
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'article_index')]
public function index(Request $request, ManagerRegistry $doctrine): Response
{
    $propertySearch = new PropertySearch();
    $form = $this->createForm(PropertySearchType::class, $propertySearch);
    $form->handleRequest($request);

    $articles = $doctrine->getRepository(Article::class)->findAll();
    
    if ($form->isSubmitted() && $form->isValid()) {
        $nom = $propertySearch->getNom();
        $category = $propertySearch->getCategory();

        $repository = $doctrine->getRepository(Article::class);
        $queryBuilder = $repository->createQueryBuilder('a');

        if ($nom) {
            $queryBuilder->andWhere('a.nom LIKE :nom')
                ->setParameter('nom', '%'.$nom.'%');
        }

        if ($category) {
            $queryBuilder->andWhere('a.category = :category')
                ->setParameter('category', $category);
        }

        $articles = $queryBuilder->getQuery()->getResult();
    }
    
    return $this->render('article/index.html.twig', [
        'form' => $form->createView(),
        'articles' => $articles
    ]);
}

    #[Route('/article/new', name: 'article_new')]
public function new(Request $request, ManagerRegistry $doctrine): Response
{
    $article = new Article();
    $form = $this->createForm(ArticleType::class, $article);
    
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $doctrine->getManager();
        $entityManager->persist($article);
        $entityManager->flush();
        
        return $this->redirectToRoute('article_index');
    }
    
    return $this->render('article/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function edit(Request $request, Article $article, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('article_index');
        }
        
        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/delete/{id}', name: 'article_delete')]
    public function delete(Article $article, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        
        return $this->redirectToRoute('article_index');
    }

    #[Route('/category/new', name: 'category_new')]
    public function newCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            
            return $this->redirectToRoute('article_index');
        }
        
        return $this->render('article/newCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/art_cat', name: 'article_par_cat')]
    public function articlesParCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);
    
        $articles = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categorySearch->getCategory();
            $articles = $category 
                ? $doctrine->getRepository(Article::class)->findBy(['category' => $category])
                : $doctrine->getRepository(Article::class)->findAll();
        }
    
        return $this->render('article/articlesParCategorie.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
    #[Route('/art_prix', name: 'article_par_prix')]
    public function articlesParPrix(Request $request, ManagerRegistry $doctrine): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);
    
        $articles = [];
    
        if ($form->isSubmitted() && $form->isValid()) {
            $minPrice = $priceSearch->getMinPrice() ?? 0;
            $maxPrice = $priceSearch->getMaxPrice() ?? PHP_FLOAT_MAX;
            
            $articles = $doctrine->getRepository(Article::class)
                ->createQueryBuilder('a')
                ->where('a.prix BETWEEN :min AND :max')
                ->setParameter('min', $minPrice)
                ->setParameter('max', $maxPrice)
                ->getQuery()
                ->getResult();
        }
    
        return $this->render('article/articlesParPrix.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
}