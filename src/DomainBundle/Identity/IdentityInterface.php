<?php

namespace DomainBundle\Identity;


/**
 * Interface IdentityInterface
 * @package DomainBundle\Identity
 */
interface IdentityInterface
{
    /**
     * @return string
     */
    public function getId();
}