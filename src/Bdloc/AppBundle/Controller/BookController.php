<?php

namespace Bdloc\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

//Ajout des uses nécessaires
use Bdloc\AppBundle\Entity\Book;
use Bdloc\AppBundle\Form\BookSearchType;

class BookController extends Controller
{
    /**
     * @Route("/catalogue/{page}")
     */
    public function allBooksAction($page)
    {
     	// Paramètres pour la vue
     	$params = array();

		// Ajout du formulaire de tri
    	$book = new Book();
        $bookSearchForm = $this->createForm(new BookSearchType(), $book);

        $request = $this->getRequest();
        $bookSearchForm->handleRequest($request);

        // Si le formulaire de recherche est soumis
		if ( $bookSearchForm->isValid() ) {
			// Je recherche par mots clés dans le titre
			$books = $this->getDoctrine()
					      ->getRepository('BdlocAppBundle:Book')
   						  ->findBookWithTitle($book->getTitle());

  		// Init de variables
     	$params['nombrePage'] = "";

		} 

		// En temps normal
		else {

		// Je récupère les BDs, 10 par page
      	$books = $this->getDoctrine()
                      ->getManager()
                      ->getRepository('BdlocAppBundle:Book')
                      ->getBooks(10, $page);

        // Jusqu'au max de BDs
        $nbBooks =  $this->getDoctrine()
                  ->getManager()
                  ->getRepository('BdlocAppBundle:Book')
                  ->countBooks();

        $params['nombrePage'] = ceil($nbBooks/10);

      	}

        // Paramètres pour twig
        $params['books'] = $books;
        $params['page'] = $page;
        $params['bookSearchForm'] = $bookSearchForm->createView();

      // j'envoie à la vue
      return $this->render("catalogue.html.twig", $params);
    }
}