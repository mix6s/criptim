<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 30.06.2017
 * Time: 11:32
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\Entity\Player;
use Domain\ValueObject\UserId;
use DomainBundle\Entity\PlayerMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @UniqueEntity(
 *     fields={"usernameCanonical", "emailCanonical"},
 *     errorPath="email",
 *     message="fos_user.email.already_used",
 * 	   groups={"AppRegistration"}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class User extends \FOS\UserBundle\Model\User
{
	public const ROLE_ADMIN = 'ROLE_ADMIN';

	public const ROLE_INVESTOR = 'ROLE_INVESTOR';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="userId", unique=true, nullable=true)
	 * @var UserId
	 */
	private $domainUserId;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param $role
	 * @return bool
	 */
	public function isGranted($role)
	{
		return in_array($role, $this->getRoles());
	}

	/**
	 * @param string $username
	 * @return \FOS\UserBundle\Model\UserInterface
	 */
	public function setEmail($username)
	{
		$this->setUsername($username);

		return parent::setEmail($username);
	}

	/**
	 * @param string $usernameCanonical
	 * @return \FOS\UserBundle\Model\UserInterface
	 */
	public function setEmailCanonical($usernameCanonical)
	{
		$this->setUsernameCanonical($usernameCanonical);

		return parent::setEmailCanonical($usernameCanonical);
	}

	/**
	 * @return UserId|null
	 */
	public function getDomainUserId()
	{
		return $this->domainUserId;
	}

	/**
	 * @param UserId $domainUserId
	 */
	public function setDomainUserId(UserId $domainUserId)
	{
		$this->domainUserId = $domainUserId;
	}
}