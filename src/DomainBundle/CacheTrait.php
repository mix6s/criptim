<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 11.10.2017
 * Time: 20:25
 */

namespace DomainBundle;


use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * Trait CacheTrait
 * @package DomainBundle
 */
trait CacheTrait
{
	private $cache;

	/**
	 * @param TagAwareAdapter $cache
	 */
	public function setCache(TagAwareAdapter $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * @return TagAwareAdapter
	 */
	public function getCache(): TagAwareAdapter
	{
		return $this->cache;
	}
}