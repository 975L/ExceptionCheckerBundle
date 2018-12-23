<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity ExceptionChecker (linked to DB table `exception_checker`)
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 *
 * @ORM\Table(name="exception_checker", indexes={@ORM\Index(name="un_exception_checker", columns={"name", "url"})})
 * @ORM\Entity(repositoryClass="c975L\ExceptionCheckerBundle\Repository\ExceptionCheckerRepository")
 */
class ExceptionChecker
{
    /**
     * ExceptionChecker unique id
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Url for the ExceptionChecker
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    protected $url;

    /**
     * Kind (deleted, excluded, redirected) of ExceptionChecker
     * @var string
     *
     * @ORM\Column(name="kind", type="string", length=24)
     * @Assert\Choice(choices = {"deleted", "excluded", "redirected"})
     */
    protected $kind;

    /**
     * Kind of redirect (Asset, Route, Url) for ExceptionChecker
     * @var string|null
     *
     * @ORM\Column(name="redirect_kind", type="string", length=24, nullable=true)
     * @Assert\Choice(choices = {null, "Asset", "Route", "Url"})
     */
    protected $redirectKind;

    /**
     * RedirectData needed by redirectKind (Route, Url, parameters, etc.)
     * @var string|null
     *
     * @ORM\Column(name="redirect_data", type="string", length=255, nullable=true)
     */
    protected $redirectData;

    /**
     * DateTime of creation for ExceptionChecker
     * @var DateTime
     *
     * @ORM\Column(name="creation", type="datetime")
     */
    protected $creation;

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     * @param string
     * @return ExceptionChecker
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set kind
     * @param string
     * @return ExceptionChecker
     */
    public function setKind($kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Get kind
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set redirectKind
     * @param string|null
     * @return ExceptionChecker
     */
    public function setRedirectKind($redirectKind = null)
    {
        $this->redirectKind = $redirectKind;

        return $this;
    }

    /**
     * Get redirectKind
     * @return string|null
     */
    public function getRedirectKind()
    {
        return $this->redirectKind;
    }

    /**
     * Set redirectData
     * @param string|null
     * @return ExceptionChecker
     */
    public function setRedirectData($redirectData = null)
    {
        $this->redirectData = $redirectData;

        return $this;
    }

    /**
     * Get redirectData
     * @return string|null
     */
    public function getRedirectData()
    {
        return $this->redirectData;
    }

    /**
     * Set creation
     * @param DateTime
     * @return ExceptionChecker
     */
    public function setCreation($creation)
    {
        $this->creation = $creation;

        return $this;
    }

    /**
     * Get creation
     * @return DateTime
     */
    public function getCreation()
    {
        return $this->creation;
    }
}