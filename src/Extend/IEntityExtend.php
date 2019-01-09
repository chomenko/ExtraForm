<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 03.01.2019
 */

namespace Chomenko\ExtraForm\Extend;

use Chomenko\ExtraForm\EntityForm;
use Nette\Forms\Controls\BaseControl;

interface IEntityExtend
{

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param BaseControl $control
	 * @param EntityForm $entityForm
	 * @return mixed
	 */
	public function attached(BaseControl $control, EntityForm $entityForm);

	/**
	 * @var object $entity
	 * @var mixed $value
	 * @return mixed
	 */
	public function executeData(object $entity, ExtendValue $value);

}
