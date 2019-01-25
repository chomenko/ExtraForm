<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Examples\EntityForm;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class UserEntity
{

	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(name="`nickname`", type="string", unique=true)
	 */
	private $nickname;

	/**
	 * @var string
	 * @ORM\Column(name="`name`", type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(name="`surname`", type="string")
	 */
	private $surname;

	/**
	 * @var string
	 * @ORM\Column(name="`email`", type="string")
	 */
	private $email;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="`register_date`", type="datetime")
	 */
	private $registerDate;

	public function __construct()
	{
		$this->registerDate = new \DateTime();
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return $this
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getNickname(): string
	{
		return $this->nickname;
	}

	/**
	 * @param string $nickname
	 * @return $this
	 */
	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getRegisterDate(): \DateTime
	{
		return $this->registerDate;
	}

	/**
	 * @param \DateTime $registerDate
	 * @return $this
	 */
	public function setRegisterDate(\DateTime $registerDate)
	{
		$this->registerDate = $registerDate;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName(string $name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSurname(): string
	{
		return $this->surname;
	}

	/**
	 * @param string $surname
	 * @return $this
	 */
	public function setSurname(string $surname)
	{
		$this->surname = $surname;
		return $this;
	}

}
