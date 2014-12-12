<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

//Ajout des uses nécessaires
use Bdloc\AppBundle\Entity\Book;
use Bdloc\AppBundle\Form\BookSearchType;
use Bdloc\AppBundle\Form\BookFilterType;

class BookController extends Controller
{
    /**
     * @Route("/catalogue/{page}/{nombreParPage}/{direction}/{entity}")
     */
    public function allBooksAction($page, $nombreParPage, $direction, $entity)
    {
     	// Paramètres pour la vue
     	$params = array();

		  // un book
    	$book = new Book();

    	   // Ajout du formulaire de recherche
        $bookSearchForm = $this->createForm(new BookSearchType(), $book);

        // Ajout du formulaire de tri
        $bookFilterForm = $this->createForm(new BookFilterType(), $book);

        // Récupération des requêtes
        $request = $this->getRequest();
        $bookSearchForm->handleRequest($request);
        $bookFilterForm->handleRequest($request);

        // Si le formulaire de tri est soumis
        if ( $bookFilterForm->isValid() ) {

        	$books = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('BdlocAppBundle:Book')
                          ->getBooks($page, $nombreParPage = $bookFilterForm->get('nombre')->getData(), $direction = $bookFilterForm->get('direction')->getData(), $entity = $bookFilterForm->get('entity')->getData(), $series = $bookFilterForm->get('series')->getData() );

	        // Jusqu'au max de BDs
	        $nbBooks =  $this->getDoctrine()
	                  		 ->getManager()
	                  		 ->getRepository('BdlocAppBundle:Book')
	                 		 ->countBooks();

	        $params['nombrePage'] = ceil($nbBooks/$nombreParPage);

        } else {

	        // Je récupère les BDs, 10 par page en temps normal
	      	$books = $this->getDoctrine()
	                      ->getManager()
	                      ->getRepository('BdlocAppBundle:Book')
	                      ->getBooks($page, $nombreParPage);

	        // Jusqu'au max de BDs
	        $nbBooks =  $this->getDoctrine()
	                  		 ->getManager()
	                  		 ->getRepository('BdlocAppBundle:Book')
	                 		 ->countBooks();

	        $params['nombrePage'] = ceil($nbBooks/$nombreParPage);

        }

        // Si le formulaire de recherche est soumis
		if ( $bookSearchForm->isValid() ) {
			// Je recherche par mots clés dans le titre
			$books = $this->getDoctrine()
					      ->getRepository('BdlocAppBundle:Book')
   						  ->findBookWithTitle($book->getTitle());

  		// Init de variables
     	$params['nombrePage'] = "";

		}

    
 

        // Paramètres pour la vue
  		$params['books'] = $books;
  		$params['page'] = $page;
      $params['nombreParPage'] = $nombreParPage;
  		$params['direction'] = $direction;
  		$params['entity'] = $entity;
  		$params['bookSearchForm'] = $bookSearchForm->createView();
  		$params['bookFilterForm'] = $bookFilterForm->createView();
   


      // j'envoie à la vue
      return $this->render("catalogue.html.twig", $params);

    }

    /**
     * @Route("/details/{id}")
     */
    public function detailsAction($id)
    {
    	$bookRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Book");
      $book = $bookRepo->find($id);

      $userRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:User");
      $user = $this->getUser();

      $cartRepo = $this->getDoctrine()->getRepository("BdlocAppBundle:Cart");
      $cart = $cartRepo->findOneBy(
             array('user'=>$user,'status'=>"courant")
      );


      $params = array(
          "book" => $book,
          "cart" => $cart           
      );

      return $this->render("details.html.twig", $params);
    }
}
