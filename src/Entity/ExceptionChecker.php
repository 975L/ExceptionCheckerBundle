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
use c975L\ExceptionCheckerBundle\Repository\ExceptionCheckerRepository;

use function PHPSTORM_META\type;

/**
 * Entity ExceptionChecker (linked to DB table `exception_checker`)
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
#[ORM\Table(name: 'exception_checker')]
#[ORM\Index(name: "exception_checker_url_idx", columns: ["url"])]
#[ORM\Entity(repositoryClass: ExceptionCheckerRepository::class)]
class ExceptionChecker
{
    /**
     * ExceptionChecker unique id
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    protected $id;

    /**
     * Url for the ExceptionChecker
     * @var string
     */
    #[ORM\Column(length: 255, type: 'string', unique: true)]
    protected $url;

    /**
     * Kind (deleted, excluded, ignored, redirected) of ExceptionChecker
     * @var string
     */
    #[ORM\Column(length: 24, type: 'string')]
    #[Assert\Choice(['deleted', 'excluded', 'ignored', 'redirected'])]
    protected $kind;

    /**
     * Kind of redirect (Asset, Route, Url) for ExceptionChecker
     * @var string|null
     */
    #[ORM\Column(length: 24, type: 'string', nullable: true)]
    #[Assert\Choice(['null', 'Asset', 'Route', 'Url'])]
    protected $redirectKind;

    /**
     * RedirectData needed by redirectKind (Route, Url, parameters, etc.)
     * @var string|null
     */
    #[ORM\Column(length: 255, type: 'string', nullable: true)]
    protected $redirectData;

    /**
     * DateTime of creation for ExceptionChecker
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $creation;

    /**
     * Get id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set url
     * @param string
     * @return ExceptionChecker
     */
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set kind
     * @param string
     * @return ExceptionChecker
     */
    public function setKind(?string $kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Get kind
     * @return string
     */
    public function getKind(): ?string
    {
        return $this->kind;
    }

    /**
     * Set redirectKind
     * @param string|null
     * @return ExceptionChecker
     */
    public function setRedirectKind(?string $redirectKind = null)
    {
        $this->redirectKind = $redirectKind;

        return $this;
    }

    /**
     * Get redirectKind
     */
    public function getRedirectKind(): ?string
    {
        return $this->redirectKind;
    }

    /**
     * Set redirectData
     * @param string|null
     * @return ExceptionChecker
     */
    public function setRedirectData(?string $redirectData = null)
    {
        $this->redirectData = $redirectData;

        return $this;
    }

    /**
     * Get redirectData
     */
    public function getRedirectData(): ?string
    {
        return $this->redirectData;
    }

    /**
     * Set creation
     * @param DateTime
     * @return ExceptionChecker
     */
    public function setCreation(?DateTime $creation)
    {
        $this->creation = $creation;

        return $this;
    }

    /**
     * Get creation
     * @return DateTime
     */
    public function getCreation(): ?DateTime
    {
        return $this->creation;
    }
}
