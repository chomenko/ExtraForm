<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Extend;

use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Exception\Exception;
use Kdyby\Doctrine\EntityManager;
use Nette\Forms\Controls\BaseControl;

abstract class EntityExtend implements IEntityExtend
{

	/**
	 * @var BaseControl $control
	 */
	protected $control;

	/**
	 * @var EntityForm
	 */
	protected $form;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var bool
	 */
	protected $attached = FALSE;

	/**
	 * @param BaseControl $control
	 * @param EntityForm $entityForm
	 */
	public function attached(BaseControl $control, EntityForm $entityForm)
	{
		$this->control = $control;
		$this->form = $entityForm;
		$this->entityManager = $entityForm->getEntityManager();

		$this->attached = TRUE;
	}

	/**
	 * @param object $entity
	 * @param ExtendValue $value
	 * @return mixed|void
	 * @throws Exception
	 */
	public function executeData(object $entity, ExtendValue $value)
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}
	}

	/**
	 * @return BaseControl
	 * @throws Exception
	 */
	public function getControl(): BaseControl
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}
		return $this->control;
	}

	/**
	 * @return EntityForm
	 * @throws Exception
	 */
	public function getForm(): EntityForm
	{
		if (!$this->attached) {
			throw Exception::notAttached(get_class($this));
		}
		return $this->form;
	}

}
