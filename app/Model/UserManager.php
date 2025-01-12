<?php declare(strict_types = 1);

namespace App\Model;

use App\Model\Entities\User;
use App\Model\Repositories\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Nette;
use Nette\Security\Passwords;

/**
 * User management.
 */
final class UserManager implements Nette\Security\Authenticator
{

	use Nette\SmartObject;

	private UserRepository $userRepository;

	public function __construct(
		private EntityManagerDecorator $em,
		private Passwords $passwords
	)
	{
		$this->userRepository = $this->em->getRepository(User::class);
	}

	/**
	 * Performs an authentication.
	 *
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(string $username, string $password): Nette\Security\IIdentity
	{
		$user = $this->userRepository->findOneBy(['username' => $username]);

		if (!$user instanceof User) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IdentityNotFound);
		}

		if (!$this->passwords->verify($password, $user->getPassword())) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::InvalidCredential);
		}

		if ($this->passwords->needsRehash($user->getPassword())) {
			$user->setPassword($password);
		}

		$this->em->flush();

		return new Nette\Security\SimpleIdentity(
			$user->getId(),
			[
				$user->getRole(),
			],
			[
				'username' => $user->getUsername(),
			]
		);
	}

	/**
	 * Adds new user.
	 *
	 * @throws DuplicateNameException
	 */
	public function add(string $username, string $email, string $password): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			$user = new User();

			$user->setUsername($username);
			$user->setEmail($email);
			$user->setPassword($password);

			$this->em->persist($user);
			$this->em->flush();
		} catch (UniqueConstraintViolationException) {
			throw new DuplicateNameException();
		}
	}

}
