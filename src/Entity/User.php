<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'cet email est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Vous devez entrer un email', groups: ['register'])]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est pas un email valide !',
    )]

    private ?string $email;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[ORM\Column(type:'string')]
    #[Assert\NotBlank(message:'Vous devez entrez un mot de passe', groups:['register'])]
    #[Assert\Length(
        min:8,
        minMessage:'Votre mot de passe doit faire un minimum de 8 caractères',
    )]
    #[Assert\Regex(
        pattern: '#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#',
        match: true,
        message: 'Le mot de passe doit contenir au moin 1 chiffre, 1 lettre minuscule, 1 lettre majuscule et doit faire au moins 8 caractères',
    )]

    private ?string $password;

    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'Les deux mots de passe ne sont pas identiques',
    )]

    public ?string $password_confirm;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(
        message: 'Vous devez renseigner votre prénom',
        groups: ['register']
    )]
    #[Assert\Length(    
        min: 3,
        max: 20,
        minMessage: 'Le prénom doit faire plus que {{ limit }} caractères',
        maxMessage: 'Le prénom ne peut pas faire plus que {{ limit }} caractères',
    )]
    private ?string $firstName;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }
}
