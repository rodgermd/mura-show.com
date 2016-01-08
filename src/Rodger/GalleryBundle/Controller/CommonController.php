<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CommonController extends Controller
{
    public $user;
    /** @var Session $session */
    public $session;
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    public $em;

    const FILTER_TAGS_KEY = 'filter.tags';
    const FILTER_YEAR_KEY = 'filter.year';

    public function preExecute()
    {
        $token         = $this->get('security.token_storage')->getToken();
        $this->user    = ($token && is_object($token->getUser())) ? $token->getUser() : null;
        $this->em      = $this->getDoctrine()->getManager();
        $this->session = $this->get('session');
    }

    /**
     * @Route("/keywords/autocomplete", name="keywords.autocomplete", requirements={"_method"="get"})
     */
    public function keywordsAutocompleteAction()
    {
        $search_string = $this->getRequest()->get('search_string');
        if (!$search_string) {
            throw $this->createNotFoundException();
        }

        $keywords = $this->em->getRepository('RodgerGalleryBundle:Tag')->search($search_string);
        $result   = array();
        foreach ($keywords as $keyword) {
            $result[] = array('label' => (string)$keyword);
        }
        $response = $this->getJsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    public function getJsonResponse()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $response;
    }

    /**
     * Sets filter tags
     *
     * @param array $tags
     */
    public function set_filter_tags(array $tags)
    {
        $this->session->set(self::FILTER_TAGS_KEY, $tags);
    }

    /**
     * Gets filter tags
     *
     * @return array
     */
    public function get_filter_tags()
    {
        return $this->session->get(self::FILTER_TAGS_KEY, array());
    }


    public function get_filters()
    {
        $this_year_tags = array_map(
            function ($t) {
                return $t->getName();
            },
            $this->getDoctrine()
                ->getRepository('RodgerGalleryBundle:Tag')
                ->getFilteredAlbumsTags($this->user, $this->get_selected_year())
        );

        return array(
            'year' => $this->get_selected_year(),
            'tags' => array_intersect($this_year_tags, $this->get_filter_tags())
        );
    }

    /**
     * Gets filter year
     *
     * @return type
     */
    public function get_selected_year()
    {
        return $this->session->get(self::FILTER_YEAR_KEY);
    }

    /**
     * Sets filter year
     */
    public function set_selected_year($year)
    {
        $this->session->set(self::FILTER_YEAR_KEY, $year);
    }
}
