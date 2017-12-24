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
	const ROLE_ADMIN = 'ROLE_ADMIN';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

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

}