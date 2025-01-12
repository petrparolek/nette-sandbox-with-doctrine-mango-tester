<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Forms;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;

final class SignPresenter extends BasePresenter
{

	#[Persistent]
	public string $backlink = '';

	public function __construct(
		private Forms\SignInFormFactory $signInFactory,
		private Forms\SignUpFormFactory $signUpFactory
	)
	{
		parent::__construct();
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
	}

	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		return $this->signInFactory->create(function (): void {
			$this->restoreRequest($this->backlink);
			$this->redirect('Homepage:');
		});
	}

	/**
	 * Sign-up form factory.
	 */
	protected function createComponentSignUpForm(): Form
	{
		return $this->signUpFactory->create(function (): void {
			$this->redirect('Homepage:');
		});
	}

}
