<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace Newsletter2Go\ContaoSync;


use GuzzleHttp\Exception\ClientException;
use Newsletter2Go\Api\Model\NewsletterRecipient;


class Hooks extends AbstractHelper
{

    /**
     * Add receiver to Newsletter2Go
     *
     * @param string $email      The recipient's email
     * @param array  $recipients An array containing the recipient ids (multiple ids if multiple channels selected)
     * @param array  $channels   An array containing the channel ids (multiple ids if multiple channels selected)
     */
    public function activateRecipient($email, $recipients, $channels)
    {
        $receiver = new NewsletterRecipient();
        $receiver->setApiCredentials(self::getApiCredentials());
        $receiver->setListId(self::getListId());
        $receiver->setEmail($email);
        $receiver->save();

        foreach ($channels as $cid) {
            /** @type \Model $channel */
            $channel = \NewsletterChannelModel::findByPk($cid);

            if ($channel->n2g_group_id) {
                try {
                    $receiver->addToGroup($channel->n2g_group_id);
                } catch (ClientException $e) {
                    \System::log(
                        sprintf('Could not activate/insert recipient %s to N2G group %u. %s', $email, $channel->n2g_group_id, $e->getMessage()),
                        __METHOD__,
                        TL_ERROR
                    );
                }
            }
        }
    }


    /**
     * Delete receiver from Newsletter2Go
     *
     * @param string $email  The recipient's e mail address
     * @param array  $remove An array containing the channel ids to remove
     */
    public function removeRecipient($email, $remove)
    {
        $recipient = new NewsletterRecipient();
        $recipient->setApiCredentials(self::getApiCredentials());
        $recipient->setListId(self::getListId());
        $recipient->setEmail($email);
        $recipient->save();

        foreach ($remove as $cid) {
            /** @type \Model $channel */
            $channel = \NewsletterChannelModel::findByPk($cid);

            if ($channel->n2g_group_id) {
                try {
                    $recipient->removeFromGroup($channel->n2g_group_id);
                } catch (ClientException $e) {
                    \System::log(
                        sprintf('Could not delete recipient %s from CR group %u. %s', $email, $channel->cr_group_id, $e->getMessage()),
                        __METHOD__,
                        TL_ERROR
                    );
                }
            }
        }
    }


    public function syncNewsletterRecipientsWithReceivers($dc)
    {

        // Only synchronize in list view
//		if ($dc->id)
//		{
//			return;
//		}

//		/** @type \Model $objNewsletterChannel */
//		$objNewsletterChannel = \NewsletterChannelModel::findByPk($dc->id);
//
//		$a = Groups::getInstance()->getReceiversForGroup($objNewsletterChannel->cr_group_id);
//		dump($a);
//
//		foreach ($a as $objReceiver)
//		{
//			$objNew = new \NewsletterRecipientsModel();
//			$objNew->
//		}

    }
}