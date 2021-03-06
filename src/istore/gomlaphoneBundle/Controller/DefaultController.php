<?php

namespace istore\gomlaphoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use istore\gomlaphoneBundle\Entity\Customer;

class DefaultController extends Controller
{
    public function indexAction(Request $request , Customer $customer = null)
    {
        //var_dump($customer);die;
        /*
        $factory = $this->get('security.encoder_factory');
        $user = new \istore\SecurityBundle\Entity\User();

        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword('ryanpass', $user->getSalt());
        $user->setPassword($password);
        */
       //var_dump($_SERVER);die;
        
        //$user = $this->get('security.context')->getToken()->getUser();
        //var_dump($user);die;
        return $this->render('istoregomlaphoneBundle:Default:index.html.twig', array(
            'datetime'      => new \DateTime(),
            'customer'      => $customer
        ));
    }
}
