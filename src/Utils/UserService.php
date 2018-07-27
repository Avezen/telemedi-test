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
            return "Konto o podanym loginie już istnieje";
        }

        if($registerData->getPassword() === $registerData->getPasswordRepeated()) {
            $user = new User();
            $roles = array("0"=>"ROLE_USER");
            $user->setLogin($registerData->getLogin());
            $user->setPassword($registerData->getPassword());
            $user->setUsername($registerData->getUsername());
            $user->setRoles(serialize($roles));
            $user->setRegistrationDate($now);
            $user->setIsActive(0);

            $this->em->persist($user);
            $this->em->flush();

            return true;
        }

        return "Podane hasła nie są identyczne";
    }

    public function loginUser($loginData){
        $loggedUser = $this->em->getRepository(User::class);

        $loggedUser = $loggedUser->createQueryBuilder('u')
            ->andWhere('u.login = :login')
            ->andWhere('u.password = :password')
            ->setParameter('login', $loginData->getLogin())
            ->setParameter('password', $loginData->getPassword())
            ->getQuery()
            ->getArrayResult();

        if($loggedUser !== []){
            if($loggedUser[0]['isActive'] === 0)
                return 0;

            return $loggedUser;
        }



        return false;
    }
}