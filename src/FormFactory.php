<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 08.01.2019
 */

namespace Chomenko\ExtraForm;

use Kdyby\Doctrine\EntityManager;

class FormFactory
{

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var FormEvents
	 */
	protected $formEvents;

	/**
	 * @param EntityManager $entityManager
	 * @param FormEvents $formEvents
	 */
	public function __construct(EntityManager $entityManager, FormEvents $formEvents)
	{
		$this->entityManager = $entityManager;
		$this->formEvents = $formEvents;
	}

	/**
	 * @return ExtraForm
	 */
	public function createForm(): ExtraForm
	{
		return new ExtraForm(null, null, $this->formEvents);
	}

	/**
	 * @param $entity
	 * @return EntityForm
	 * @throws \Exception
	 */
	public function createEntityForm($entity): EntityForm
	{
		return new EntityForm($entity, $this->entityManager, $this->formEvents);
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

	/**
	 * @return FormEvents
	 */
	public function getFormEvents(): FormEvents
	{
		return $this->formEvents;
	}

}
