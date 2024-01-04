<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
   
    private UserPasswordHasherInterface $hasher;
 
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function PrePersist(User $user)
    {
        $this->encodePasswords($user);
    }
    public function PreUpdate(User $user)
    {
        $this->encodePasswords($user);
    }
    /** 
     * Encoder le password recuperer dans le plainPassword generer aleatoirement
     * @param User $user
     * @return void
     */
    public function encodePasswords(User $user)
    {
      
        if ($user->getPlainPassword() === null) {
            $plainPassword = 'password';
        }
        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );
        $user->eraseCredentials();
    }
}
