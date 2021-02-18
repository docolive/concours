<?php
namespace App\Service;
use App\Entity\Concours;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ConcoursSession
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function stocke(Concours $concours)
    {
        // stores an attribute in the session for later reuse
        $this->session->set('concours', $concours);

        // // gets an attribute by name
        // $foo = $this->session->get('foo');

        // // the second argument is the value returned when the attribute doesn't exist
        // $filters = $this->session->get('filters', []);

        // // ...
    }

    public function recup()
    {
        
        // gets an attribute by name
         return $this->session->get('concours','vide');

        // // the second argument is the value returned when the attribute doesn't exist
        // $filters = $this->session->get('filters', []);

        // // ...
    }
}
