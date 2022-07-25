<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Security;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for EsceptionChecker access
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class ExceptionCheckerVoter extends Voter
{
    /**
     * Used for access to config
     * @var string
     */
    final public const CONFIG = 'c975LExceptionChecker-config';

    /**
     * Used for access to create an ExecptionChecker
     * @var string
     */
    final public const CREATE = 'c975LExceptionChecker-create';

    /**
     * Used for access to dashboard
     * @var string
     */
    final public const DASHBOARD = 'c975LExceptionChecker-dashboard';

    /**
     * Used for access to delete an ExecptionChecker
     * @var string
     */
    final public const DELETE = 'c975LExceptionChecker-delete';

    /**
     * Used for access to display an ExecptionChecker
     * @var string
     */
    final public const DISPLAY = 'c975LExceptionChecker-display';

    /**
     * Used for access to duplicate an ExecptionChecker
     * @var string
     */
    final public const DUPLICATE = 'c975LExceptionChecker-duplicate';

    /**
     * Used for access to help
     * @var string
     */
    final public const HELP = 'c975LExceptionChecker-help';

    /**
     * Used for access to modify an ExecptionChecker
     * @var string
     */
    final public const MODIFY = 'c975LExceptionChecker-modify';

    /**
     * Contains all the available attributes to check with in supports()
     * @var array
     */
    private const ATTRIBUTES = [self::CONFIG, self::CREATE, self::DASHBOARD, self::DELETE, self::DISPLAY, self::DUPLICATE, self::HELP, self::MODIFY];

    public function __construct(
        /**
         * Stores ConfigServiceInterface
         */
        private readonly ConfigServiceInterface $configService,
        /**
         * Stores AccessDecisionManagerInterface
         */
        private readonly AccessDecisionManagerInterface $decisionManager
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if (null !== $subject) {
            return $subject instanceof ExceptionChecker && in_array($attribute, self::ATTRIBUTES);
        }

        return in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::CONFIG, self::CREATE, self::DASHBOARD, self::DELETE, self::DISPLAY, self::DUPLICATE, self::HELP, self::MODIFY => $this->decisionManager->decide($token, [$this->configService->getParameter('c975LExceptionChecker.roleNeeded', 'c975l/exceptionchecker-bundle')]),
            default => throw new LogicException('Invalid attribute: ' . $attribute),
        };
    }
}
