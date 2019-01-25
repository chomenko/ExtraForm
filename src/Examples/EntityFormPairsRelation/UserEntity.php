<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Examples\EntityFormPairsRelation;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity()
 */
class UserEntity
{

	use Identifier;

	/**
	 * @var string
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 50,
	 *      minMessage = "Your nickname must be at least {{ limit }} characters long",
	 *      maxMessage = "Your nickname cannot be longer than {{ limit }} characters"
	 * )
	 * @ORM\Column(name="`nickname`", type="string", unique=true)
	 */
	private $nickname;

	/**
	 * @var string
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 30,
	 *      minMessage = "Your name must be at least {{ limit }} characters long",
	 *      maxMessage = "Your name cannot be longer than {{ limit }} characters"
	 * )
	 * @ORM\Column(name="`name`", type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @Assert\Length(
	 *      min = 2,
	 *      max = 50,
	 *      minMessage = "Your surname must be at least {{ limit }} characters long",
	 *      maxMessage = "Your surname cannot be longer than {{ limit }} characters"
	 * )
	 * @ORM\Column(name="`surname`", type="string")
	 */
	private $surname;

	/**
	 * @var string
	 * @Assert\Email(
	 *     message = "The email '{{ value }}' is not a valid email.",
	 *     checkMX = true
	 * )
	 * @ORM\Column(name="`email`", type="string")
	 */
	private $email;

	/**
	 * @var \DateTime
	 * @Assert\DateTime(format="d.m.Y H:i")
	 * @ORM\Column(name="`active_to`", type="datetime", nullable=true)
	 */
	private $activeTo;

	/**
	 * @var \DateTime
	 * @Assert\DateTime(format="d.m.Y H:i")
	 * @ORM\Column(name="`register_date`", type="datetime")
	 */
	private $registerDate;

	/**
	 * @var ArrayCollection|UserCountryEntity[]
	 *
	 * @ORM\OneToMany(targetEntity="Examples\EntityFormPairsRelation\UserCountryEntity", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
	 */
	private $countries;

	public function __construct()
	{
		$this->registerDate = new \DateTime();
		$this->countries = new ArrayCollection();
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
	 * @return \DateTime
	 */
	public function getActiveTo(): ?\DateTime
	{
		return $this->activeTo;
	}

	/**
	 * @param \DateTime|null $activeTo
	 * @return $this
	 */
	public function setActiveTo(\DateTime $activeTo = NULL)
	{
		$this->activeTo = $activeTo;
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

	/**
	 * @return Collection
	 */
	public function getCountries(): ?Collection
	{
		return $this->countries;
	}

	/**
	 * @param UserCountryEntity $relation
	 * @return $this
	 */
	public function addCountry(UserCountryEntity $relation)
	{
		$this->countries->add($relation);
		return $this;
	}

}
