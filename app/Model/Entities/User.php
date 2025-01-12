<?php declare(strict_types = 1);

namespace App\Model\Entities;

use App\Model\Repositories\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Nette\Security\Passwords;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'username', columns: ['username'])]
class User
{

	#[ORM\Id]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[ORM\GeneratedValue]
	private int $id;

	#[ORM\Column(type:'string', length: 100, nullable: false)]
	private string $username;

	#[ORM\Column(type:'string', length: 100, nullable: false)]
	private string $password;

	#[ORM\Column(type:'string', length: 100, nullable: false)]
	private string $email;

	#[ORM\Column(type:'string', length: 100, nullable: true)]
	private ?string $role;

	public function getId(): int
	{
		return $this->id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getRole(): ?string
	{
		return $this->role;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function setPassword(string $password): void
	{
		$passwords = new Passwords();
		$this->password = $passwords->hash($password);
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function setRole(?string $role): void
	{
		$this->role = $role;
	}

}
