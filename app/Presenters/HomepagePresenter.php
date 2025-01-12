<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Model\Entities\User;
use App\Model\EntityManagerDecorator;
use App\Model\Repositories\UserRepository;

final class HomepagePresenter extends BasePresenter
{

	private UserRepository $userRepository;

	public function __construct(private EntityManagerDecorator $em)
	{
		parent::__construct();

		$this->userRepository = $this->em->getRepository(User::class);
	}

	public function renderDefault(): void
	{
		$user = $this->userRepository->get(1);
		bdump($user);
		$this->template->anyVariable = 'any value';
	}

}
