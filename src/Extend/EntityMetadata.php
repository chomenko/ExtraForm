<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 22.12.2018
 */

namespace Chomenko\ExtraForm\Extend;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Nette\ComponentModel\IComponent;

class EntityMetadata
{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var object
	 */
	private $entity;

	/**
	 * @var mixed
	 */
	private $originalValue;

	public function __construct(object $entity, IComponent $component, ClassMetadata $metadata, UnitOfWork $unitOfWork)
	{
		$this->entity = $entity;
		$this->component = $component;
		$this->unitOfWork = $unitOfWork;
		$this->metadata = $metadata;
	}
}
