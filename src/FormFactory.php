<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Kdyby\Doctrine\EntityManager;
use Nette\DI\Container;

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
	 * @var Container
	 */
	protected $container;

	public function __construct()
	{
	}

	/**
	 * @param Container $container
	 */
	public function injectContainer(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @param EntityManager $entityManager
	 */
	public function injectEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param FormEvents $formEvents
	 */
	public function injectFormEvents(FormEvents $formEvents)
	{
		$this->formEvents = $formEvents;
	}

	/**
	 * @return ExtraForm
	 */
	public function createForm(): ExtraForm
	{
		return new ExtraForm(NULL, NULL, $this->formEvents);
	}

	/**
	 * @param string|object $entity
	 * @return EntityForm
	 * @throws \Exception
	 */
	public function createEntityForm($entity): EntityForm
	{
		return new EntityForm($entity, $this->container, $this->entityManager, $this->formEvents);
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
