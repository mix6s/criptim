<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.09.2017
 * Time: 20:08
 */

namespace Domain\Entity;

use Domain\Exception\DomainException;
use Domain\ValueObject\GameType;


/**
 * Class Game
 * @package Domain\Entity
 */
class Game implements \JsonSerializable
{
	use MetadataTrait;

	const STATE_DEFAULT = 0;
	const STATE_FINISHED = 1;

	private $id;
	private $datetime;
	private $type;
	private $place;
	private $season;
	private $seasonTeamA;
	private $seasonTeamB;
	private $state;
	private $membersA;
	private $membersB;

	/**
	 * Game constructor.
	 * @param int $id
	 * @param \DateTime $datetime
	 * @param GameType $type
	 * @param string $place
	 * @param Season $season
	 * @param SeasonTeam $seasonTeamA
	 * @param SeasonTeam $seasonTeamB
	 */
	private function __construct(
		int $id,
		\DateTime $datetime,
		GameType $type,
		string $place,
		Season $season,
		SeasonTeam $seasonTeamA,
		SeasonTeam $seasonTeamB
	) {
		$this->id = $id;
		$this->modify($datetime, $type, $place, $season, $seasonTeamA, $seasonTeamB, self::STATE_DEFAULT);
	}

	/**
	 * @param int $id
	 * @param \DateTime $datetime
	 * @param GameType $type
	 * @param string $place
	 * @param Season $season
	 * @param SeasonTeam $seasonTeamA
	 * @param SeasonTeam $seasonTeamB
	 * @return Game
	 */
	public static function create(
		int $id,
		\DateTime $datetime,
		GameType $type,
		string $place,
		Season $season,
		SeasonTeam $seasonTeamA,
		SeasonTeam $seasonTeamB
	) {
		return new self($id, $datetime, $type, $place, $season, $seasonTeamA, $seasonTeamB);
	}

	/**
	 * @param \DateTime $datetime
	 * @param GameType $type
	 * @param string $place
	 * @param Season $season
	 * @param SeasonTeam $seasonTeamA
	 * @param SeasonTeam $seasonTeamB
	 * @param int $state
	 * @throws DomainException
	 */
	public function modify(
		\DateTime $datetime,
		GameType $type,
		string $place,
		Season $season,
		SeasonTeam $seasonTeamA,
		SeasonTeam $seasonTeamB,
		int $state
	) {
		if ($season->getId() != $seasonTeamA->getSeason()->getId()
			|| $season->getId() != $seasonTeamB->getSeason()->getId()) {
			throw new DomainException("seasonteams seasons dont equal season");
		}

		if ($seasonTeamA->getId() === $seasonTeamB->getId()) {
			throw new DomainException("seasonteams equal");
		}
		$this->datetime = $datetime;
		$this->type = $type;
		$this->place = $place;
		$this->season = $season;
		$this->seasonTeamA = $seasonTeamA;
		$this->seasonTeamB = $seasonTeamB;
		$this->state = $state;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	function jsonSerialize()
	{
		return [
			'id' => $this->getId(),
			'place' => $this->getPlace(),
			'datetime' => $this->getDatetime()->format('Y-m-d H:i'),
			'type' => (string)$this->getType(),
			'season' => $this->getSeason(),
			'seasonteamA' => $this->getSeasonTeamA(),
			'seasonteamB' => $this->getSeasonTeamB(),
			'metadata' => $this->getMetadata(),
			'state' => $this->getState(),
			'membersA' => $this->getMembersA(),
			'membersB' => $this->getMembersB()
		];
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	public function getDatetime(): \DateTime
	{
		return $this->datetime;
	}

	/**
	 * @return GameType
	 */
	public function getType(): GameType
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getPlace(): string
	{
		return $this->place;
	}

	/**
	 * @return Season
	 */
	public function getSeason(): Season
	{
		return $this->season;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeamA(): SeasonTeam
	{
		return $this->seasonTeamA;
	}

	/**
	 * @return SeasonTeam
	 */
	public function getSeasonTeamB(): SeasonTeam
	{
		return $this->seasonTeamB;
	}

	/**
	 * @return int
	 */
	public function getState(): int
	{
		return $this->state;
	}

	/**
	 * @return mixed
	 */
	public function getMembersA(): array
	{
		if (!is_array($this->membersA)) {
			return [];
		}
		return $this->membersA;
	}

	/**
	 * @return mixed
	 */
	public function getMembersB(): array
	{
		if (!is_array($this->membersB)) {
			return [];
		}
		return $this->membersB;
	}

	/**
	 * @param array $membersA
	 */
	public function setMembersA(array $membersA)
	{
		$this->membersA = $membersA;
	}

	/**
	 * @param array $membersB
	 */
	public function setMembersB(array $membersB)
	{
		$this->membersB = $membersB;
	}
}