<?php

namespace App\Utils;


use App\Entity\User;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Session\Session;

class UserService{

    protected $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function registerUser($registerData){
        $now = new \DateTime(date("Y-m-d"));

        $doUserExist = $this->em->getRepository(User::class)->findBy(array("login"=>$registerData->getLogin()));
        if($doUserExist !== []){
            return "Account with this login already exists";
        }
        if(strlen($registerData->getLogin()) > 5 || strlen($registerData->getPassword()) > 5) {
            if ($registerData->getPassword() === $registerData->getPasswordRepeated()) {
                $user = new User();
                $roles = array("0"=>"ROLE_USER");
                $user->setLogin($registerData->getLogin());
                $user->setPassword(password_hash($registerData->getPassword(), PASSWORD_BCRYPT, array('cost' => 11)));
                $user->setUsername($registerData->getUsername());
                $user->setRoles(serialize($roles));
                $user->setRegistrationDate($now);
                $user->setIsActive(0);
                $this->em->persist($user);
                $this->em->flush();

                return true;
            }

            return "Passwords are not the same";
        }
        return "Password or login are too short. They need to be longer than 5";
    }

    public function loginUser($loginData){
        $loggedUser = $this->em->getRepository(User::class);

        $loggedUser = $loggedUser->createQueryBuilder('u')
            ->andWhere('u.login = :login')
            ->setParameter('login', $loginData->getLogin())
            ->getQuery()
            ->getArrayResult();

        if($loggedUser !== []){
            if(password_verify($loginData->getPassword(), $loggedUser[0]['password'])) {
                if ($loggedUser[0]['isActive'] === 0)
                    return 0;

                return $loggedUser;
            }
        }
        return false;
    }
}
