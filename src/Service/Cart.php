<?php

namespace App\Service;

use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    protected $session;
    protected $em;
    
    /**
     * @var ArrayCollection
     */
    private $movies;
    
    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
        $this->movies = $this->session->get('cart', new ArrayCollection());
    }
    
    /**
     * @param Movie $movie
     * @return boolean
     */
    public function add(Movie $movie)
    {
        if ($this->has($movie)) {
            return false;
        }
        
        $this->movies->set($movie->getId(), 1);
        $this->session->set('cart', $this->movies);
        
        return true;
    }
    
    /**
     * @param Movie $movie
     * @return boolean
     */
    public function has(Movie $movie)
    {
        return $this->movies->containsKey($movie->getId());
    }
    
    /**
     * @param Movie $movie
     * @return boolean
     */
    public function remove(Movie $movie)
    {
        if (!$this->has($movie)) {
            return false;
        }
        
        $this->movies->remove($movie->getId());
        $this->session->set('cart', $this->movies);
        
        return true;
    }
    
    public function clear()
    {
        $this->session->set('cart', new ArrayCollection());
    }
    
    /**
     * @return array of Movie
     */
    public function getMovies()
    {
        $movie_ids = $this->movies->getKeys();
        
        return $this->em->getRepository(Movie::class)
            ->findBy(array('id' => $movie_ids));
    }
}