<?php

/**
 * xNavigation - Highly extendable and flexible navigation module for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    xNavigation
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\XNavigation\Content;

use Bit3\Contao\XNavigation\Event\CreateDefaultConditionEvent;
use Bit3\Contao\XNavigation\Model\ConditionModel;
use Bit3\Contao\XNavigation\XNavigationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultSubscriber
 */
class DefaultSubscriber implements EventSubscriberInterface
{
    /**
	 * {@inheritdoc}
	 */
    public static function getSubscribedEvents()
    {
        return array(
            XNavigationEvents::CREATE_DEFAULT_CONDITION => 'createDefaultCondition',
        );
    }

    public function createDefaultCondition(CreateDefaultConditionEvent $event)
    {
        $root          = new ConditionModel();
        $root->pid     = $event->getCondition()->id;
        $root->sorting = 128;
        $root->type    = 'and';
        $root->save();

        // content type
        $condition                          = new ConditionModel();
        $condition->pid                     = $root->id;
        $condition->sorting                 = 128;
        $condition->type                    = 'item_type';
        $condition->item_type_accepted_type = 'content';
        $condition->save();

        // content published
        $condition          = new ConditionModel();
        $condition->pid     = $root->id;
        $condition->sorting = 256;
        $condition->type    = 'content_published';
        $condition->save();

        // login status
        $or          = new ConditionModel();
        $or->pid     = $root->id;
        $or->sorting = 512;
        $or->type    = 'or';
        $or->save();

        {
            // unprotected contents
            $and          = new ConditionModel();
            $and->pid     = $or->id;
            $and->sorting = 128;
            $and->type    = 'and';
            $and->save();

            {
                // login status -> not protected
                $condition                                            = new ConditionModel();
                $condition->pid                                       = $and->id;
                $condition->sorting                                   = 128;
                $condition->type                                      = 'content_protected';
                $condition->content_members_accepted_protected_status = '';
                $condition->save();

                // login status -> not logged in
                $condition                                     = new ConditionModel();
                $condition->pid                                = $and->id;
                $condition->sorting                            = 256;
                $condition->type                               = 'member_login';
                $condition->member_login_accepted_login_status = 'logged_out';
                $condition->save();

                // login status -> content guests only
                $condition                                        = new ConditionModel();
                $condition->pid                                   = $and->id;
                $condition->sorting                               = 512;
                $condition->type                                  = 'content_guests';
                $condition->content_guests_accepted_guests_status = '';
                $condition->save();
            }
        }

        {
            // protected contents
            $and          = new ConditionModel();
            $and->pid     = $or->id;
            $and->sorting = 256;
            $and->type    = 'and';
            $and->save();

            {
                // login status -> protected
                $condition                                            = new ConditionModel();
                $condition->pid                                       = $and->id;
                $condition->sorting                                   = 128;
                $condition->type                                      = 'content_protected';
                $condition->content_members_accepted_protected_status = '';
                $condition->save();

                // login status -> content groups
                $condition          = new ConditionModel();
                $condition->pid     = $and->id;
                $condition->sorting = 256;
                $condition->type    = 'content_groups';
                $condition->save();
            }
        }
    }
}
