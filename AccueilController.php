<?php

namespace App\Controller;

use DateTime;
use App\Constante\Constantes;
use App\Repository\TicketRepository;
use App\Repository\VenteRepository;
use OpenApi\Annotations\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AccueilController extends AbstractController
{
    private $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager = null, TicketRepository $ticketRepository, VenteRepository $venteRepository)
    {
        $this->tokenManager = $tokenManager;
        $this->ticketRepository = $ticketRepository;
        $this->venteRepository = $venteRepository;
    }

    /**
     * @Route("/", name="accueil")
     */
    public function accueil()
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $role = $roles[0];
        $dataTicket = [];
        $dataVente = [];
        $day = (new DateTime())->setTime(0, 0);

        if ($role === Constantes::ROLE_SUPER_ADMIN || $role === Constantes::ROLE_ADMIN) {


            $grapheTickets = $this->ticketRepository->getGrapheTicket($user->getStructure()->getId(), $day);
            $grapheVentes = $this->venteRepository->getGrapheVente($user->getStructure()->getId(), $day);

            if (!$grapheTickets) {
                //$montantGrapheTicket = intval($grapheTickets[0]['nombre']);
                //$response = new Response($montant);
                //return $response;
                $resultat = "";
                $classe = "";
            }
            $dataTicket = array();
            foreach ($grapheTickets as $value) {
                $dataTicket[] = array(
                    'nombre' => $value['nombreGrapheTicket'],
                    'montant' => $value['montantGrapheTicket'],
                    'date' => $value['dateGrapheTicket']
                );
            }

            if (!$grapheVentes) {
                //$montantGrapheTicket = intval($grapheTickets[0]['nombre']);
                //$response = new Response($montant);
                //return $response;
                $resultat = "";
                $classe = "";
            }
            $dataVente = array();
            foreach ($grapheVentes as $value) {
                $dataVente[] = array(
                    'nombre' => $value['nombreGrapheVente'],
                    'montant' => $value['montantGrapheVente'],
                    'date' => $value['dateGrapheVente'],
                );
            }
        } else {

            $grapheTickets = null;
            $grapheVentes = null;
            // return new Response("ok");
        }



        return $this->render('user/accueil.html.twig', [
            'grapheTickets' => $dataTicket,
            'grapheVentes' => $dataVente,
            'controller_name' => 'AccueilController'
        ]);
    }
}
