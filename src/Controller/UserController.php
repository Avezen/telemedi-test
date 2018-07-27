<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{

    /**
     * @Route("/getallusers", name="get_all_users")
     */
    public function getAllUsers()
    {
        $session = new Session();

        if($session->get('userRoles')[0] === "ROLE_ADMIN") {
            $em = $this->getDoctrine()->getManager();

            $users = $em->getRepository(User::class)->createQueryBuilder('u')
                ->getQuery()
                ->getArrayResult();

            if(count($users) !== 0) {
                for ($i = 0; $i < count($users); $i++) {
                    $users[$i]['registrationDate'] = $users[$i]['registrationDate']->format('Y-m-d');
                }
                return new JsonResponse($users);
            }
            return new JsonResponse(array("response"=>"No user exists"));
        }

        return new JsonResponse(array("response"=>"Access denied"));
    }

    /**
     * @Route("/deleteuser", name="delete_user")
     */
    public function deleteUser(Request $request)
    {
        $session = new Session();
        $id = json_decode($request->getContent(), true);

        if($session->get('userRoles')[0] === "ROLE_ADMIN"){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);

            if($user !== null){
                $em->remove($user);
                $em->flush();

                return new JsonResponse(array("response"=>"User deleted successfully"));
            }
            return new JsonResponse(array("response"=>"No user with id: ".$id));
        }
        return new JsonResponse(array("response"=>"Access denied"));
    }

    /**
     * @Route("/changeuserstatus", name="change_user_status")
     */
    public function changeUserStatus(Request $request)
    {
        $session = new Session();
        $id = json_decode($request->getContent(), true);

        if($session->get('userRoles')[0] === "ROLE_ADMIN"){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);

            if($user !== null){
                if($user->getIsActive()===1){
                    $user->setIsActive(0);
                }else {
                    $user->setIsActive(1);
                }

                $em->persist($user);
                $em->flush();

                return new JsonResponse(array("response"=>"User status changed successfully"));
            }
            return new JsonResponse(array("response"=>"No user with id: ".$id));
        }
        return new JsonResponse(array("response"=>"Access denied"));
    }
}
