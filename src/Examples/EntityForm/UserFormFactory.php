<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 08.01.2019
 */

namespace Examples\EntityForm;

use Chomenko\AutoInstall\AutoInstall;
use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\FormFactory;

class UserFormFactory implements AutoInstall
{

	/**
	 * @var FormFactory @inject
	 */
	public $formFactory;

	public function create(UserEntity $user = null): EntityForm
	{
		if(!$user){
			$user = UserEntity::class;
		}

		$form = $this->formFactory->createEntityForm($user);

		$form->addText('nickname', 'Nickname');
		$form->addText('email', 'Email')
			->addRule(EntityForm::EMAIL);
		$form->addText('name', 'Name');
		$form->addText('surname', 'Surname');
		$form->addSubmit('send', 'Send');

		$form->onSuccess[] = [$this, 'formSuccess'];

		return $form;
	}

	public function formSuccess(EntityForm $form, array $values)
	{
		/** @var UserEntity $entity */
		$entity = $form->getData();
		$this->em->persist($entity);
		$this->em->flush();
	}

}
