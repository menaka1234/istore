<?php

namespace istore\gomlaphoneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use istore\gomlaphoneBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Session\Session;
use istore\gomlaphoneBundle\Controller\AuthenticatedController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomerController extends Controller //implements AuthenticatedController
{
    public function __construct() 
    {
        //$session = new Session();
        //$session->start();

        //$request = new Request();
        //echo $request->query->get('lang');
        //$language = (null == $request->query->get('lang')) ? $request->query->get('lang') : 'en';
        //echo $language;die;
        //$session->set('language', $language);
        //echo $request->setLocale($language);
    }

    public function indexPrepaidAction(Request $request)
    {
        
        $user = $this->getUser();
        
        /*if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->render('istoregomlaphoneBundle::unauthorized.html.twig', array());
        }*/
        
        $currentPage = (int) ($request->query->get('page') ? $request->query->get('page') : 1);
        
        $count = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(c) AS total_customers')
            ->from('istoregomlaphoneBundle:Customer', 'c')
            ->join('istoregomlaphoneBundle:Store', 's' , 'WITH' , 'c.customer_store=s.id')
            ->where('s.id=?1')
            ->andWhere('c.customer_type=?2')
            ->setParameter(1, $user->getStoreId())
            ->setParameter(2, 'prepaid')
            ->getQuery()
            ->getSingleResult();
    
        $paginator = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('istoregomlaphoneBundle:Customer', 'c')
            ->join('istoregomlaphoneBundle:Store', 's' , 'WITH' , 'c.customer_store=s.id')
            ->where('s.id=?1')
            ->andWhere('c.customer_type=?2')
            ->setParameter(1, $user->getStoreId())
            ->setParameter(2, 'prepaid')
            ->getQuery()
            ->setFirstResult($currentPage==1 ? 0 : ($currentPage-1)*10)
            ->setMaxResults(10)
            ->getScalarResult();
        
        //var_dump($paginator);die;
        
        return $this->render('istoregomlaphoneBundle:Customer:index.html.twig', array(
            'customers'      => $paginator,
            'total_customers'=> $count['total_customers'],
            'total_pages'     => ceil($count['total_customers']/10),
            'current_page'    => $currentPage,
            'action'    => 'prepaid',
            'controller' => 'customer',
        ));
    }
    
    public function indexPostpaidAction(Request $request)
    {
        
        $user = $this->getUser();
        
        /*if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->render('istoregomlaphoneBundle::unauthorized.html.twig', array());
        }*/
        
        $currentPage = (int) ($request->query->get('page') ? $request->query->get('page') : 1);
        
        $count = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(c) AS total_customers')
            ->from('istoregomlaphoneBundle:Customer', 'c')
            ->join('istoregomlaphoneBundle:Store', 's' , 'WITH' , 'c.customer_store=s.id')
            ->where('s.id=?1')
            ->andWhere('c.customer_type=?2')
            ->setParameter(1, $user->getStoreId())
            ->setParameter(2, 'postpaid')
            ->getQuery()
            ->getSingleResult();
    
        $paginator = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('istoregomlaphoneBundle:Customer', 'c')
            ->join('istoregomlaphoneBundle:Store', 's' , 'WITH' , 'c.customer_store=s.id')
            ->where('s.id=?1')
            ->andWhere('c.customer_type=?2')
            ->setParameter(1, $user->getStoreId())
            ->setParameter(2, 'postpaid')
            ->getQuery()
            ->setFirstResult($currentPage==1 ? 0 : ($currentPage-1)*10)
            ->setMaxResults(10)
            ->getScalarResult();
        
        //var_dump($paginator);die;
        
        return $this->render('istoregomlaphoneBundle:Customer:index.html.twig', array(
            'customers'      => $paginator,
            'total_customers'=> $count['total_customers'],
            'total_pages'     => ceil($count['total_customers']/10),
            'current_page'    => $currentPage,
            'action'    => 'postpaid',
            'controller' => 'customer',
        ));
    }
    
    public function prepaidAction(Request $request, Customer $customer) {
        
        $user = $this->getUser();
        
        $currentPage = (int) ($request->query->get('page') ? $request->query->get('page') : 1);
        
        $countPrepaid = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(s) AS total_sales')
            ->from('istoregomlaphoneBundle:Sale', 's')
            ->leftJoin('istoregomlaphoneBundle:Postpaid', 'po' , 'WITH' , 'po.postpaid_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Customer', 'cu' , 'WITH' , 's.sale_customer_id=cu.id')
            ->join('istoregomlaphoneBundle:Store', 'st' , 'WITH' , 's.sale_store_id=st.id')
            ->where('cu.id=?1')
            ->andWhere('po.id IS NULL')
            ->andWhere('st.id=?2')
            ->setParameter(1, $customer->getId())
            ->setParameter(2, $user->getStoreId())
            ->getQuery()
            ->getSingleResult();
    
        $paginatorPrepaid = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('s , cu , SUM(i.item_sell_price) as s_sale_total')
            ->from('istoregomlaphoneBundle:Sale', 's')
            ->leftJoin('istoregomlaphoneBundle:Postpaid', 'po' , 'WITH' , 'po.postpaid_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Customer', 'cu' , 'WITH' , 's.sale_customer_id=cu.id')
            ->join('istoregomlaphoneBundle:SaleItem', 'si' , 'WITH' , 'si.saleitem_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Item', 'i', 'WITH', 'si.saleitem_item_id=i.id')
            ->join('istoregomlaphoneBundle:Bulk', 'b' , 'WITH' , 'i.item_bulk=b.id')
            ->join('istoregomlaphoneBundle:Model', 'm' , 'WITH' , 'b.bulk_model=m.id')
            ->join('istoregomlaphoneBundle:Category', 'c' , 'WITH' , 'm.model_category=c.id')
            ->join('istoregomlaphoneBundle:Store', 'st' , 'WITH' , 's.sale_store_id=st.id')
            ->where('cu.id=?1')
            ->andWhere('po.id IS NULL')
            ->andWhere('st.id=?2')
            ->setParameter(1, $customer->getId())
            ->setParameter(2, $user->getStoreId())
            ->groupBy('s.id')
            ->getQuery()
            ->setFirstResult($currentPage==1 ? 0 : ($currentPage-1)*10)
            ->setMaxResults(10)
            ->getScalarResult();
        
//var_dump($paginator);die;
        
        return $this->render('istoregomlaphoneBundle:Customer:transactions.html.twig', array(
            'sales'      => $paginatorPrepaid,
            'customer'   => $customer,
            'total_sales'=> $countPrepaid['total_sales'],
            'total_pages'     => ceil($countPrepaid['total_sales']/10),
            'current_page'    => $currentPage,
            "action" => "prepaid",
            "controller" => "customer"
        ));
    }
    
    
    public function postpaidAction(Request $request, Customer $customer) {
        
        $user = $this->getUser();
        
        $currentPage = (int) ($request->query->get('page') ? $request->query->get('page') : 1);
        
        $countPostpaid = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(DISTINCT s.id) AS total_sales')
            ->from('istoregomlaphoneBundle:Sale', 's')
            ->join('istoregomlaphoneBundle:Postpaid', 'po' , 'WITH' , 'po.postpaid_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Store', 'st' , 'WITH' , 's.sale_store_id=st.id')
            ->where('s.sale_customer_id=?1')
            ->setParameter(1, $customer->getId())
            ->andWhere('st.id=?2')
            ->setParameter(2, $user->getStoreId())
            //->groupBy('s.id')
            ->getQuery()
            ->getScalarResult();
        
        $paginatorPostpaid = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('DISTINCT s , cu , SUM(i.item_sell_price) AS s_sale_total')
            ->from('istoregomlaphoneBundle:Sale', 's')
            ->join('istoregomlaphoneBundle:SaleItem', 'si' , 'WITH' , 'si.saleitem_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Item', 'i', 'WITH', 'si.saleitem_item_id=i.id')
            ->join('istoregomlaphoneBundle:Bulk', 'b' , 'WITH' , 'i.item_bulk=b.id')
            ->join('istoregomlaphoneBundle:Customer', 'cu' , 'WITH' , 's.sale_customer_id=cu.id')
            ->join('istoregomlaphoneBundle:Postpaid', 'po' , 'WITH' , 'po.postpaid_sale_id=s.id')
            ->join('istoregomlaphoneBundle:Store', 'st' , 'WITH' , 's.sale_store_id=st.id')
            ->where('cu.id=?1')
            ->setParameter(1, $customer->getId())
            ->andWhere('st.id=?2')
            ->setParameter(2, $user->getStoreId())
            ->groupBy('s.id')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->setFirstResult($currentPage==1 ? 0 : ($currentPage-1)*10)
            ->setMaxResults(10)
            ->getScalarResult();

        foreach ($paginatorPostpaid as &$temp){
            $postpaid = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('SUM(po.postpaid_amount) AS total_paid')
                ->from('istoregomlaphoneBundle:Postpaid', 'po')
                ->where('po.postpaid_sale_id=?1')
                ->setParameter(1, $temp['s_id'])
                ->getQuery()
                ->getSingleResult();
            $temp['po_total_paid'] = $postpaid['total_paid'];
            
            $sale = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('SUM(i.item_sell_price) AS total_sale')
                ->from('istoregomlaphoneBundle:Sale', 's')
                ->join('istoregomlaphoneBundle:SaleItem', 'si' , 'WITH' , 'si.saleitem_sale_id=s.id')
                ->join('istoregomlaphoneBundle:Item', 'i', 'WITH', 'si.saleitem_item_id=i.id')
                ->join('istoregomlaphoneBundle:Bulk', 'b' , 'WITH' , 'i.item_bulk=b.id')
                ->where('s.id=?1')
                ->setParameter(1, $temp['s_id'])
                ->getQuery()
                //->setFirstResult($currentPage==1 ? 0 : ($currentPage-1)*10)
                //->setMaxResults(10)
                ->getSingleResult();
            $temp['s_sale_total'] = $sale['total_sale'];
        }
//var_dump($paginatorPostpaid);die;
        
        return $this->render('istoregomlaphoneBundle:Customer:transactions.html.twig', array(
            'sales'      => $paginatorPostpaid,
            'customer'   => $customer,
            'total_sales'=> $countPostpaid[0]['total_sales'],
            'total_pages'     => ceil($countPostpaid[0]['total_sales']/10),
            'current_page'    => $currentPage,
            "action" => "postpaid",
            "controller" => "customer"
        ));
    }
    
    public function addAction(Request $request)
    {
        $user = $this->getUser();
        
        /*if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->render('istoregomlaphoneBundle::unauthorized.html.twig', array());
        }*/
        
        if( $request->getMethod() == 'POST')
        {
            $customer = new Customer();
            $customer->setCustomerName($request->request->get('customerName'));
            $customer->setCustomerPhone($request->request->get('customerPhone'));
            $customer->setCustomerType($request->request->get('customerType'));
            //$customer->setCustomerNotes($request->request->get('customerNotes'));
            //$customerStore = $this->getDoctrine()
            //        ->getRepository('istoregomlaphoneBundle:Store')
            //        ->find($user->getStoreId());
            $customer->setCustomerStore($user->getStoreId());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('istoregomlaphone_customer_index_'.$request->request->get('customerType')));
        }
        
        return $this->render('istoregomlaphoneBundle:Customer:add.html.twig' , array(
            'action'  => 'add',
            'controller' => 'customer',
        ));
    }
    
    public function editAction(Request $request, Customer $customer)
    {
        
        if( $request->getMethod() == 'POST')
        {
            $customer->setCustomerName($request->request->get('customerName'));
            $customer->setCustomerPhone($request->request->get('customerPhone'));
            $customer->setCustomerType($request->request->get('customerType'));
            //$customer->setCustomerNotes($request->request->get('customerNotes'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('istoregomlaphone_customer_index_'.$request->request->get('customerType')));
        }
        
        return $this->render('istoregomlaphoneBundle:Customer:edit.html.twig' , array(
            "customer" => $customer,
            'action'  => 'edit',
            'controller' => 'customer',
        ));
    }
    
    public function findAction(Request $request)
    {
        //echo $request->request->get('serial');die;
        $customer = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('istoregomlaphoneBundle:Customer', 'c')
            ->where('c.customer_phone = ?1')
            ->orWhere('c.customer_name LIKE ?2')
            ->setParameter(1 , $request->request->get('phone'))
            ->setParameter(2 , '%'.$request->request->get('name').'%')
            ->getQuery()
            ->getScalarResult();
        
//var_dump($customer);die;
        
        if(count($customer)){
            return new JsonResponse(array('count' => count($customer) , 'customer' => $customer[0]));
        } else {
            return new JsonResponse(array('count' => count($customer)));
        }
        
    }
    
    public function queryAction(Request $request)
    {
        $user = $this->getUser();
        //echo $request->request->get('serial');die;
        if($request->query->get('param') === 'phone'){
            $customer = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('c.customer_phone AS value , c.customer_name AS data')
                ->from('istoregomlaphoneBundle:Customer', 'c')
                ->where('c.customer_phone LIKE ?1')
                ->andWhere('c.customer_store = ?2')
                ->setParameter(1 , '%'.$request->query->get('query').'%')
                ->setParameter(2 , $user->getStoreId())
                ->getQuery()
                ->getScalarResult();
            
        } elseif ($request->query->get('param') === 'name'){
            $customer = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('c.customer_name AS value , c.customer_phone AS data')
                ->from('istoregomlaphoneBundle:Customer', 'c')
                ->where('c.customer_name LIKE ?1')
                ->andWhere('c.customer_store = ?2')
                ->setParameter(1 , '%'.$request->query->get('query').'%')
                ->setParameter(2 , $user->getStoreId())
                ->getQuery()
                ->getScalarResult();
        }
        
        return new JsonResponse(array(
            'query' => $request->query->get('query') ,
            'suggestions' => $customer
        ));
        
    }
    
    public function validateAction(Request $request)
    {
        
        $user = $this->getUser();
        
        /*if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->render('istoregomlaphoneBundle::unauthorized.html.twig', array());
        }*/
        
        //var_dump($request);die;
        $customerNew['customerId'] = $request->request->get('customerId');
        $customerNew['customerPhone'] = $request->request->get('customerPhone');
        
        $action = $request->request->get('action');
        $controller = $request->request->get('controller');
//echo $controller.'/'.$action;die;
        $error = null;
        if($customerNew['customerPhone'] == '')
            $error = 'is_null';
        
        $customer = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('cu')
            ->from('istoregomlaphoneBundle:Customer', 'cu')
            ->where('cu.customer_phone = ?1')
            ->setParameter(1 , $customerNew['customerPhone'])
            ->andWhere('cu.customer_store = ?2')
            ->setParameter(2 , $user->getStoreId())
            ->getQuery()
            ->getScalarResult();
//var_dump($category);die;
        
        //Customer exists
        if(count($customer)) {
            if($action === 'add')
                $error = 'customer_exists';
                
            elseif($action === 'edit' && $customer[0]['cu_id'] != $customerNew['customerId'])
                $error = 'customer_exists';
                
            else 
                $error = 'not_found';
        //Category does not exist
        } else {
            $error = 'not_found';
        }
    //var_dump($category);die;
        return new JsonResponse(array('error' => $error , 'customer' => $customer[0]));
        
    }
    
    public function deleteAction(Request $request, Customer $customer)
    {
        
        $user = $this->getUser();
        
        if(!in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->render('istoregomlaphoneBundle::unauthorized.html.twig', array());
        }
        
        try{
            if (!$customer) {
                throw $this->createNotFoundException('No customer found');
            }
            
            $sales = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('s')
            ->from('istoregomlaphoneBundle:Sale', 's')
            ->where('s.sale_customer_id = ?1')
            ->setParameter(1 , $customer->getId())
            ->getQuery()
            ->getScalarResult();
            
            if(!count($sales)){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($customer);
                $entityManager->flush();
                return new JsonResponse(array('error' => 0 , 'message' => 'Customer has been successfully deleted'));
            } else {
                return new JsonResponse(array('error' => 1 , 'message' => 'Can not delete customer that already has transactions'));
            }

            
        } catch (DBALException $e){
            return new JsonResponse(array('error' => 1 , 'message' => 'Can not delete customer that already has transactions'));
        }
    }
}
